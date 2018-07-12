<?php

namespace AppBundle\Command\Photo;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use AppBundle\Criticalmass\Image\PhotoGps\PhotoGps;
use AppBundle\Criticalmass\Image\PhotoUploader\PhotoUploader;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityManager;
use PHPExif\Reader\Reader;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
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
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ride = $this->doctrine->getRepository(Ride::class)->findByCitySlugAndRideDate($input->getArgument('citySlug'),
            $input->getArgument('rideDate'));
        $user = $this->doctrine->getRepository(User::class)->findOneByUsername($input->getArgument('username'));
        $track = $this->doctrine->getRepository(Track::class)->findByUserAndRide($ride, $user);

        $this->photoUploader
            ->setRide($ride)
            ->setUser($user)
            ->setTrack($track)
            ->addDirectory($input->getArgument('path'));

        $table = new Table($output);
        $table->setHeaders(['Filename', 'DateTime', 'Coords']);

        /** @var Photo $photo */
        foreach ($this->photoUploader->getAddedPhotoList() as $photo) {
            $table->addRow([
                $photo->getImageName(),
                $photo->getDateTime() ? $photo->getDateTime()->format('Y-m-d H:i:s') : '',
                $photo->hasCoordinates() ? sprintf('%f,%f', $photo->getLatitude(), $photo->getLongitude()) : ''
            ]);
        }

        $table->render();

    }
}
