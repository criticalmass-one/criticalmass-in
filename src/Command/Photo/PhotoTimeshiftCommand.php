<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Event\Photo\PhotoUpdatedEvent;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PhotoTimeshiftCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    public function __construct(RegistryInterface $registry, EventDispatcherInterface $eventDispatcher)
    {
        $this->registry = $registry;
        $this->eventDispatcher = $eventDispatcher;

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
                'rideDate',
                InputArgument::REQUIRED,
                'date of the ride'
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
                'photoDateTimeZone',
                InputArgument::OPTIONAL,
                'Timezone of the photos datetime values'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('photoDateTimeZone') && $input->getArgument('photoDateTimeZone')) {
            $dateTimeZone = new \DateTimeZone($input->getArgument('photoDateTimeZone'));
        } else {
            $dateTimeZone = new \DateTimeZone('UTC');
        }

        $interval = new \DateInterval($input->getArgument('dateInterval'));

        /** @var Ride $ride */
        $ride = $this->registry->getRepository(Ride::class)->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        /** @var User $user */
        $user = $this->registry->getRepository(User::class)->findOneByUsername($input->getArgument('username'));

        /** @var Track $track */
        $photos = $this->registry->getRepository(Photo::class)->findPhotosByUserAndRide($user, $ride);

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $photo->setDateTime($photo->getDateTime()->add($interval));

            $this->eventDispatcher->dispatch(PhotoUpdatedEvent::NAME, new PhotoUpdatedEvent($photo));
        }

        $this->registry->getManager()->flush();
    }
}
