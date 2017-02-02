<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CalderaBundle\Entity\User;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\DateTimeExifReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps\PhotoGps;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportImagesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var PhotoGps $photoGps */
    protected $photoGps;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var Ride $ride */
    protected $ride;

    /** @var User $user */
    protected $user;

    /** @var Track $track */
    protected $track;

    /** @var DateTimeExifReader $dter */
    protected $dter;

    /** @var PhotoGps $pgps */
    protected $pgps;

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
        $this->doctrine = $this->getContainer()->get('doctrine');

        $this->manager = $this->doctrine->getManager();

        $this->ride = $this->doctrine->getRepository('CalderaBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));
        $this->user = $this->doctrine->getRepository('CalderaBundle:User')->findOneByUsername($input->getArgument('username'));

        $this->dter = $this->getContainer()->get('caldera.criticalmass.image.exifreader.datetime');
        $this->pgps = $this->getContainer()->get('caldera.criticalmass.image.photogps');

        $this->track = $this->doctrine->getRepository('CalderaBundle:Track')->findByUserAndRide($this->ride, $this->user);

        $fileList = $this->getImageFileList($input);

        $output->writeln(sprintf('Adding photos to %s by user %s', $this->ride->getFancyTitle(), $this->user->getUsername()));

        foreach ($fileList as $file) {
            $output->writeln(sprintf('Processing image file %s', $file));

            $filename = $input->getArgument('path') . '/' . $file;

            $this->createPhotoEntity($filename);
        }
    }

    protected function getImageFileList(InputInterface $input)
    {
        $imageFileList = [];

        if ($handle = opendir($input->getArgument('path'))) {
            while (false !== ($file = readdir($handle))) {
                if (strpos($file, '.jpg') || strpos($file, '.JPG')) {
                    array_push($imageFileList, $file);
                }
            }

            closedir($handle);
        }

        return $imageFileList;
    }

    protected function calculateDateTime(Photo $photo)
    {
        $dateTime = $this
            ->dter
            ->setPhoto($photo)
            ->execute()
            ->getDateTime();

        $photo->setDateTime($dateTime);
    }

    protected function calculateLocation(Photo $photo)
    {
        if ($this->track) {
            $this
                ->pgps
                ->setPhoto($photo)
                ->setTrack($this->track)
                ->execute();
        }
    }

    protected function createPhotoEntity($sourceFilename)
    {
        $photo = new Photo();

        $imageFilename = uniqid() . '.jpg';
        $uploadDirectory = $this->getContainer()->getParameter('kernel.root_dir') . '/../web/photos/';
        $destinationFilename = $uploadDirectory . $imageFilename;

        copy($sourceFilename, $destinationFilename);

        $photo->setImageName($imageFilename);

        $photo->setUser($this->user);
        $photo->setRide($this->ride);
        $photo->setCity($this->ride->getCity());

        $this->calculateDateTime($photo);
        $this->calculateLocation($photo);

        $this->manager->persist($photo);
        $this->manager->flush();

    }
}