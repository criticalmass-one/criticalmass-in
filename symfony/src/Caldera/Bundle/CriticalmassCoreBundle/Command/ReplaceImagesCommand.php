<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
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
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var PhotoGps $photoGps
     */
    protected $photoGps;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');

        /**
         * @var PhotoGps $photoGps
         */
        $this->photoGps = $this->getContainer()->get('caldera.criticalmass.image.photogps');

        /**
         * @var Registry $manager
         */
        $this->manager = $this->doctrine->getManager();

        $ride = $this->doctrine->getRepository('CalderaBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));
        $user = $this->doctrine->getRepository('ApplicationSonataUserBundle:User')->findOneByUsername($input->getArgument('username'));

        echo $ride->getId();

        $track = $this->doctrine->getRepository('CalderaBundle:Track')->findByUserAndRide($ride, $user);
        $photos = $this->doctrine->getRepository('CalderaBundle:Photo')->findPhotosByRide($ride);

        foreach ($photos as $photo) {
            $this->photoGps->setPhoto($photo);
            $this->photoGps->setTrack($track);

            $this->photoGps->execute();

            $this->manager->merge($photo);
            $this->manager->flush();
        }
    }
}