<?php declare(strict_types=1);

namespace Tests\Controller;

use App\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

/**
 * Covers the whole magic-link login, so the InteractiveLoginEvent is dispatched
 * by the real authenticator rather than simulated.
 */
final class LastLoginTest extends AbstractControllerTestCase
{
    #[Test]
    public function loggingInViaMagicLinkStampsLastLogin(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneBy(['email' => 'testuser@criticalmass.in']);
        $user->setLastLogin(new \DateTime('2020-01-01 12:00:00'));
        $em->flush();

        $before = new \DateTime();
        $client->request('GET', $this->loginLinkFor($user));

        self::assertTrue($client->getResponse()->isSuccessful() || $client->getResponse()->isRedirection());

        $em->clear();
        $reloaded = $em->getRepository(User::class)->findOneBy(['email' => 'testuser@criticalmass.in']);

        self::assertNotNull($reloaded->getLastLogin());
        self::assertGreaterThanOrEqual(
            $before->getTimestamp(),
            $reloaded->getLastLogin()->getTimestamp(),
            'the magic link login must refresh last_login'
        );
    }

    #[Test]
    public function anonymousRequestsLeaveLastLoginAlone(): void
    {
        $client = static::createClient();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneBy(['email' => 'cyclist@criticalmass.in']);
        $user->setLastLogin($stamp = new \DateTime('2021-06-01 08:00:00'));
        $em->flush();
        $em->clear();

        $client->request('GET', '/');

        $reloaded = $em->getRepository(User::class)->findOneBy(['email' => 'cyclist@criticalmass.in']);

        self::assertSame($stamp->format('Y-m-d H:i:s'), $reloaded->getLastLogin()->format('Y-m-d H:i:s'));
    }

    private function loginLinkFor(User $user): string
    {
        /** @var LoginLinkHandlerInterface $handler */
        $handler = static::getContainer()->get('security.authenticator.login_link_handler.main');

        return $handler->createLoginLink($user)->getUrl();
    }
}
