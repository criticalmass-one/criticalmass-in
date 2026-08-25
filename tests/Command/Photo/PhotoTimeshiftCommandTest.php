<?php declare(strict_types=1);

namespace Tests\Command\Photo;

use App\Command\Photo\PhotoTimeshiftCommand;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\User;
use App\Event\Photo\PhotoUpdatedEvent;
use App\Repository\PhotoRepository;
use App\Repository\RideRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tests\Support\EntityIdHelper;

final class PhotoTimeshiftCommandTest extends TestCase
{
    private MockObject&RideRepository $rideRepository;
    private MockObject&EventDispatcherInterface $dispatcher;
    private MockObject&ObjectManager $manager;
    /** @var list<Photo> */
    private array $photos = [];
    private CommandTester $tester;
    private Ride $ride;
    private User $user;

    protected function setUp(): void
    {
        $this->ride = new Ride();
        $this->user = new User();

        foreach (['2024-05-31 19:00:00', '2024-05-31 20:30:00'] as $i => $exifDate) {
            $photo = (new Photo())->setExifCreationDate(new \DateTime($exifDate));
            $photo->setLatitude(53.5);
            $photo->setLongitude(9.9);
            EntityIdHelper::setId($photo, $i + 1);
            $this->photos[] = $photo;
        }

        $this->rideRepository = $this->createMock(RideRepository::class);

        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('__call')->willReturnCallback(
            fn (string $method, array $arguments): ?User => 'findOneByUsername' === $method && 'malte' === $arguments[0] ? $this->user : null
        );

        $photoRepository = $this->createMock(PhotoRepository::class);
        $photoRepository->method('findPhotosByUserAndRide')->with($this->user, $this->ride)->willReturn($this->photos);

        $this->manager = $this->createMock(ObjectManager::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->willReturnMap([
            [Ride::class, null, $this->rideRepository],
            [User::class, null, $userRepository],
            [Photo::class, null, $photoRepository],
        ]);
        $registry->method('getManager')->willReturn($this->manager);

        $this->dispatcher = $this->createMock(EventDispatcherInterface::class);

        $application = new Application();
        $application->addCommand(new PhotoTimeshiftCommand($registry, $this->dispatcher));

        $this->tester = new CommandTester($application->find('criticalmass:photos:timeshift'));
    }

    #[Test]
    public function shiftsExifDatesForwardByDefault(): void
    {
        $this->rideRepository->method('findByCitySlugAndRideDate')->with('hamburg', '2024-05-31')->willReturn($this->ride);
        $this->dispatcher->expects(self::exactly(2))
            ->method('dispatch')
            ->with(self::isInstanceOf(PhotoUpdatedEvent::class), PhotoUpdatedEvent::NAME)
            ->willReturnArgument(0);
        $this->manager->expects(self::once())->method('flush');

        $this->tester->execute([
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2024-05-31',
            'username' => 'malte',
            'dateInterval' => 'PT2H',
        ]);

        self::assertSame(0, $this->tester->getStatusCode());
        self::assertSame('2024-05-31 21:00:00', $this->photos[0]->getExifCreationDate()->format('Y-m-d H:i:s'));
        self::assertSame('2024-05-31 22:30:00', $this->photos[1]->getExifCreationDate()->format('Y-m-d H:i:s'));
        self::assertStringContainsString('2024-05-31 21:00:00', $this->tester->getDisplay());
    }

    #[Test]
    public function subDirectionShiftsBackwards(): void
    {
        $this->rideRepository->method('findByCitySlugAndRideDate')->willReturn($this->ride);
        $this->dispatcher->method('dispatch')->willReturnArgument(0);

        $this->tester->execute([
            'citySlug' => 'hamburg',
            'rideIdentifier' => '2024-05-31',
            'username' => 'malte',
            'dateInterval' => 'P1D',
            'direction' => 'sub',
        ]);

        self::assertSame('2024-05-30 19:00:00', $this->photos[0]->getExifCreationDate()->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function fallsBackToRideSlugWhenIdentifierIsNoDate(): void
    {
        $this->rideRepository->method('findByCitySlugAndRideDate')->willReturn(null);
        $this->rideRepository->expects(self::once())->method('findOneByCitySlugAndSlug')->with('hamburg', 'kidical-mass')->willReturn($this->ride);
        $this->dispatcher->method('dispatch')->willReturnArgument(0);

        $this->tester->execute([
            'citySlug' => 'hamburg',
            'rideIdentifier' => 'kidical-mass',
            'username' => 'malte',
            'dateInterval' => 'PT30M',
        ]);

        self::assertSame(0, $this->tester->getStatusCode());
        self::assertSame('2024-05-31 19:30:00', $this->photos[0]->getExifCreationDate()->format('Y-m-d H:i:s'));
    }
}
