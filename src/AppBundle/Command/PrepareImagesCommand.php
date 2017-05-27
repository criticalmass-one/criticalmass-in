<?php

namespace AppBundle\Command;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use AppBundle\Image\PhotoGps\PhotoGps;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareImagesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var \DateTimeZone $dateTimeZone */
    protected $dateTimeZone = null;

    protected function configure()
    {
        $this
            ->setName('criticalmass:images:prepare')
            ->setDescription('Create thumbnails for images')
            ->addArgument(
                'citySlug',
                InputArgument::REQUIRED,
                'Slug of the city'
            )
            ->addArgument(
                'rideDate',
                InputArgument::REQUIRED,
                'Date of the ride'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Username'
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
        $ride = $this->doctrine->getRepository('AppBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        /** @var User $user */
        $user = $this->doctrine->getRepository('AppBundle:User')->findOneByUsername($input->getArgument('username'));

        /** @var Track $track */
        $track = $this->doctrine->getRepository('AppBundle:Track')->findByUserAndRide($ride, $user);

        $photos = $this->doctrine->getRepository('AppBundle:Photo')->findPhotosByRide($ride);

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