<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CriticalmassCoreBundle\Leaflet\LeafletPrinter\LeafletPrinter;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrintRideCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var Ride $ride */
    protected $ride;

    /** @var array $tracks */
    protected $tracks = [];

    protected function configure()
    {
        $this
            ->setName('criticalmass:ride:print')
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');

        $this->manager = $this->doctrine->getManager();

        $this->ride = $this->doctrine->getRepository('CalderaBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        $printer = $this->getContainer()->get('caldera.criticalmass.leaflet.printer');
        $printer->setRide($this->ride);
        $printer->execute();
        
    }
}