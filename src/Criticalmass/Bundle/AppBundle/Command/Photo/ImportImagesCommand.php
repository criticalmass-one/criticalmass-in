<?php

namespace Criticalmass\Bundle\AppBundle\Command\Photo;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Bundle\AppBundle\Entity\User;
use Criticalmass\Component\Image\PhotoGps\PhotoGps;
use Criticalmass\Component\Image\PhotoUploader\PhotoUploader;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityManager;
use PHPExif\Reader\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportImagesCommand extends ContainerAwareCommand
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var PhotoUploader $photoUploader */
    protected $photoUploader;

    public function __construct(Doctrine $doctrine, PhotoUploader $photoUploader)
    {
        $this->doctrine = $doctrine;
        $this->photoUploader = $photoUploader;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:photos:import')
            ->setDescription('Import photos to a tour')
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
                'Name of the user'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path of the image directory'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ride = $this->doctrine->getRepository(Ride::class)->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));
        $user = $this->doctrine->getRepository(User::class)->findOneByUsername($input->getArgument('username'));
        $track = $this->doctrine->getRepository(Track::class)->findByUserAndRide($ride, $user);

        $this->photoUploader
            ->setRide($ride)
            ->setUser($user)
            ->setTrack($track)
            ->addDirectory($input->getArgument('path'))
        ;
    }
}
