<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Entity\User;
use App\EventSubscriber\LastLoginSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

final class LastLoginSubscriberTest extends TestCase
{
    #[Test]
    public function listensToTheInteractiveLoginEvent(): void
    {
        self::assertSame(
            [SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin'],
            LastLoginSubscriber::getSubscribedEvents()
        );
    }

    #[Test]
    public function stampsTheCurrentTimeOnTheUserAndFlushes(): void
    {
        $user = new User();
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects($this->once())->method('flush');

        $before = new \DateTime();
        $this->subscriber($manager)->onInteractiveLogin($this->event($user));
        $after = new \DateTime();

        self::assertNotNull($user->getLastLogin());
        self::assertGreaterThanOrEqual($before->getTimestamp(), $user->getLastLogin()->getTimestamp());
        self::assertLessThanOrEqual($after->getTimestamp(), $user->getLastLogin()->getTimestamp());
    }

    #[Test]
    public function overwritesAPreviousLoginTimestamp(): void
    {
        $user = (new User())->setLastLogin(new \DateTime('2020-01-01 12:00:00'));
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects($this->once())->method('flush');

        $this->subscriber($manager)->onInteractiveLogin($this->event($user));

        self::assertGreaterThan(new \DateTime('2020-01-02'), $user->getLastLogin());
    }

    #[Test]
    public function ignoresTokensCarryingAForeignUserClass(): void
    {
        $manager = $this->createMock(EntityManagerInterface::class);
        $manager->expects($this->never())->method('flush');

        $user = new InMemoryUser('someone', null);

        $this->subscriber($manager)->onInteractiveLogin($this->event($user));

        self::assertSame('someone', $user->getUserIdentifier(), 'the foreign user is left untouched');
    }

    private function subscriber(EntityManagerInterface $manager): LastLoginSubscriber
    {
        $registry = $this->createStub(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);

        return new LastLoginSubscriber($registry);
    }

    private function event(object $user): InteractiveLoginEvent
    {
        return new InteractiveLoginEvent(new Request(), $this->token($user));
    }

    private function token(object $user): TokenInterface
    {
        return new UsernamePasswordToken($user, 'main', ['ROLE_USER']);
    }
}
