<?php declare(strict_types=1);

namespace Tests\Security\Webauthn;

use App\Entity\User;
use App\EventSubscriber\LastLoginSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\InteractiveAuthenticatorInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Webauthn\Bundle\Security\Http\Authenticator\WebauthnAuthenticator;

/**
 * Passkey logins must refresh User::$lastLogin like the magic link and the
 * OAuth logins do. That works because Symfony only dispatches the
 * InteractiveLoginEvent — the event LastLoginSubscriber listens to — for
 * authenticators that declare themselves interactive.
 */
final class PasskeyLastLoginTest extends KernelTestCase
{
    #[Test]
    public function theWebauthnAuthenticatorIsInteractive(): void
    {
        $reflection = new \ReflectionClass(WebauthnAuthenticator::class);

        self::assertTrue(
            $reflection->implementsInterface(InteractiveAuthenticatorInterface::class),
            'without this interface Symfony would not dispatch InteractiveLoginEvent for passkeys'
        );

        /** @var WebauthnAuthenticator $authenticator */
        $authenticator = $reflection->newInstanceWithoutConstructor();

        self::assertTrue($authenticator->isInteractive());
    }

    #[Test]
    public function theMainFirewallUsesThatAuthenticator(): void
    {
        self::bootKernel();

        self::assertTrue(
            self::getContainer()->has('security.authenticator.webauthn.main'),
            'the passkey authenticator is expected on the main firewall'
        );
    }

    #[Test]
    public function aPasskeyLoginStampsTheUser(): void
    {
        $user = new User();
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects($this->once())->method('flush');

        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);

        $before = new \DateTime();
        (new LastLoginSubscriber($registry))->onInteractiveLogin(
            new InteractiveLoginEvent(new Request(), new UsernamePasswordToken($user, 'main', ['ROLE_USER']))
        );

        self::assertNotNull($user->getLastLogin());
        self::assertGreaterThanOrEqual($before->getTimestamp(), $user->getLastLogin()->getTimestamp());
    }
}
