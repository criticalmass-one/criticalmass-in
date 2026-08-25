<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\ProfilePhotoGenerator\ProfilePhotoGenerator;
use App\Entity\User;
use App\Event\User\UserColorChangedEvent;
use App\EventSubscriber\ProfilePhotoEventSubscriber;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProfilePhotoEventSubscriberTest extends TestCase
{
    #[Test]
    public function colorChangeRegeneratesGeneratedProfilePhoto(): void
    {
        $user = (new User())->setOwnProfilePhoto(false);

        $generator = $this->createMock(ProfilePhotoGenerator::class);
        $generator->expects(self::once())->method('setUser')->with($user)->willReturnSelf();
        $generator->expects(self::once())->method('generate')->willReturn('avatar.png');

        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::once())->method('flush');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($manager);

        (new ProfilePhotoEventSubscriber($generator, $registry))->onUserColorChange(new UserColorChangedEvent($user));
    }

    #[Test]
    public function colorChangeKeepsUploadedProfilePhoto(): void
    {
        $user = (new User())->setOwnProfilePhoto(true);

        $generator = $this->createMock(ProfilePhotoGenerator::class);
        $generator->expects(self::never())->method('generate');

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->expects(self::never())->method('getManager');

        (new ProfilePhotoEventSubscriber($generator, $registry))->onUserColorChange(new UserColorChangedEvent($user));
    }

    #[Test]
    public function subscribesToColorChangeEvent(): void
    {
        self::assertSame('onUserColorChange', ProfilePhotoEventSubscriber::getSubscribedEvents()[UserColorChangedEvent::NAME]);
    }
}
