<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\User;
use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps\PhotoGps;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReplaceImagesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var PhotoGps $photoGps */
    protected $photoGps;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var \DateTimeZone $dateTimeZone */
    protected $dateTimeZone = null;

    protected function configure()
    {
        $this
            ->setName('criticalmass:images:replaces')
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
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->photoGps = $this->getContainer()->get('caldera.criticalmass.image.photogps');
        $this->manager = $this->doctrine->getManager();

        if ($input->hasArgument('photoDateTimeZone') && $input->getArgument('photoDateTimeZone')) {
            $this->dateTimeZone = new \DateTimeZone($input->getArgument('photoDateTimeZone'));

            $this->photoGps->setDateTimeZone($this->dateTimeZone);
        }

        /** @var Ride $ride */
        $ride = $this->doctrine->getRepository('CalderaBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        /** @var User $user */
        $user = $this->doctrine->getRepository('CalderaBundle:User')->findOneByUsername($input->getArgument('username'));

        /** @var Track $track */
        $track = $this->doctrine->getRepository('CalderaBundle:Track')->findByUserAndRide($ride, $user);

        $photos = $this->doctrine->getRepository('CalderaBundle:Photo')->findPhotosByRide($ride);

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $this->photoGps->setPhoto($photo);
            $this->photoGps->setTrack($track);

            $this->photoGps->execute();
            
            $output->writeln(sprintf(
                'Updated location of photo <comment>#%d</comment> to <info>%f,%f</info>',
                $photo->getId(),
                $photo->getLatitude(),
                $photo->getLongitude()
            ));

            $this->manager->merge($photo);
        }

        $this->manager->flush();
    }
}