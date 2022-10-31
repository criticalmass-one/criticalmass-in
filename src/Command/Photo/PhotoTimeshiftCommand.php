<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Event\Photo\PhotoUpdatedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PhotoTimeshiftCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:photos:timeshift')
            ->setDescription('Timeshift photos')
            ->addArgument(
                'citySlug',
                InputArgument::REQUIRED,
                'Slug of the city'
            )
            ->addArgument(
                'rideIdentifier',
                InputArgument::REQUIRED,
                'Slug or date of the ride'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Id of the user'
            )
            ->addArgument(
                'dateInterval',
                InputArgument::REQUIRED,
                'Interval to shift'
            )
            ->addArgument(
                'direction',
                InputArgument::OPTIONAL,
                'Direction to shift',
                'add'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $interval = new \DateInterval($input->getArgument('dateInterval'));
        $modificationMethodName = $input->getArgument('direction');

        /** @var Ride $ride */
        $ride = $this->getRide($input->getArgument('citySlug'), $input->getArgument('rideIdentifier'));

        /** @var User $user */
        $user = $this->registry->getRepository(User::class)->findOneByUsername($input->getArgument('username'));

        /** @var Track $track */
        $photos = $this->registry->getRepository(Photo::class)->findPhotosByUserAndRide($user, $ride);

        $entityManager = $this->registry->getManager();

        $progressBar = new ProgressBar($output, is_countable($photos) ? count($photos) : 0);

        $table = new Table($output);
        $table->setHeaders([
            'photoId',
            'dateTime',
            'latitude',
            'longitude',
            'location',
        ]);

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $dateTimeImmutable = \DateTimeImmutable::createFromMutable($photo->getExifCreationDate());
            $dateTimeImmutable = $dateTimeImmutable->$modificationMethodName($interval);
            $photo->setExifCreationDate(new \DateTime(sprintf('@%d', $dateTimeImmutable->getTimestamp())));

            $this->eventDispatcher->dispatch(PhotoUpdatedEvent::NAME, new PhotoUpdatedEvent($photo, false));

            $table->addRow([
                $photo->getId(),
                $photo->getExifCreationDate()->format('Y-m-d H:i:s'),
                $photo->getLatitude(),
                $photo->getLongitude(),
                $photo->getLocation(),
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $table->render();

        $entityManager->flush();
    }

    protected function getRide(string $citySlug, string $rideIdentifier): ?Ride
    {
        $ride = $this->registry->getRepository(Ride::class)->findByCitySlugAndRideDate($citySlug, $rideIdentifier);

        if (!$ride) {
            $ride = $this->registry->getRepository(Ride::class)->findOneByCitySlugAndSlug($citySlug, $rideIdentifier);
        }

        return $ride;
    }
}
