<?php

namespace AppBundle\Command\Photo;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use AppBundle\Criticalmass\Image\PhotoLocator\PhotoLocator;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReplaceImagesCommand extends Command
{
    /** @var PhotoLocator $photoLocator */
    protected $photoLocator;

    /** @var Doctrine $doctrine */
    protected $doctrine;

    public function __construct(PhotoLocator $photoLocator, Doctrine $doctrine)
    {
        $this->photoLocator = $photoLocator;
        $this->doctrine = $doctrine;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:photos:relocate')
            ->setDescription('Regenerate LatLng Tracks')
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
                'photoDateTimeZone',
                InputArgument::OPTIONAL,
                'Timezone of the photos datetime values'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasArgument('photoDateTimeZone') && $input->getArgument('photoDateTimeZone')) {
            $dateTimeZone = new \DateTimeZone($input->getArgument('photoDateTimeZone'));

            $this->photoLocator->setDateTimeZone($dateTimeZone);
        }

        /** @var Ride $ride */
        $ride = $this->doctrine->getRepository(Ride::class)->findByCitySlugAndRideDate($input->getArgument('citySlug'),
            $input->getArgument('rideDate'));

        /** @var User $user */
        $user = $this->doctrine->getRepository(User::class)->findOneByUsername($input->getArgument('username'));

        /** @var Track $track */
        $track = $this->doctrine->getRepository(Track::class)->findByUserAndRide($ride, $user);

        $this->photoLocator
            ->setRide($ride)
            ->setUser($user)
            ->setTrack($track)
            ->setOutput($output)
            ->relocate();
    }
}
