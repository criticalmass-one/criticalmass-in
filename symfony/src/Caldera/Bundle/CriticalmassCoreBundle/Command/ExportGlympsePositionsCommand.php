<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Ticket;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter\GpxExporter;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportGlympsePositionsCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var RangeLatLngListGenerator $generator
     */
    protected $generator;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected function configure()
    {
        $this
            ->setName('criticalmass:positions:glympse')
            ->setDescription('Export positions as gpx file')
            ->addArgument(
                'ticketId',
                InputArgument::OPTIONAL,
                'Id of the glympse ticket to export'
            )
            ->addOption(
                'all',
                'a'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');

        $repository = $this->doctrine->getRepository('CalderaBundle:Ticket');

        if ($input->hasOption('all') && $input->getOption('all')) {
            $tickets = $repository->findBy(['exported' => false]);

            foreach ($tickets as $ticket) {
                $this->export($ticket);
            }
        } elseif ($input->hasArgument('ticketId')) {
            /**
             * @var Ticket $ticket
             */

            $ticket = $repository->find($input->getArgument('ticketId'));

            if ($ticket->getExported()) {
                $output->writeln('This ticket has already been exported.');
            } else {
                $this->export($ticket);
            }
        }
    }

    protected function export(Ticket $ticket)
    {
        $em = $this->doctrine->getManager();

        /**
         * @var GpxExporter $exporter
         */
        $exporter = $this->getContainer()->get('caldera.criticalmass.gps.gpxexporter');

        $exporter->setTicket($ticket);

        $exporter->execute();

        $gpxContent = $exporter->getGpxContent();

        if ($gpxContent) {
            $filename = uniqid() . '.gpx';

            $fp = fopen('../web/tracks/' . $filename, 'w');
            fwrite($fp, $gpxContent);
            fclose($fp);

            $track = new Track();
            $track->setTicket($ticket);
            $track->setTrackFilename($filename);
            $track->setUsername($ticket->getUsername());

            $this->loadTrackProperties($track);
            $this->generateSimpleLatLngList($track);

            if ($ticket->getRide()) {
                $track->setRide($ticket->getRide());
                $this->addRideEstimate($track, $track->getRide());
            }

            $em->persist($track);
        }

        $ticket->setExported(true);

        $em->persist($ticket);
        $em->flush();
    }

    protected function generateSimpleLatLngList(Track $track)
    {
        /**
         * @var SimpleLatLngListGenerator $generator
         */
        $generator = $this->getContainer()->get('caldera.criticalmass.gps.latlnglistgenerator.simple');
        $list = $generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);
    }

    protected function addRideEstimate(Track $track, Ride $ride)
    {
        /**
         * @var RideEstimateService $estimateService
         */
        $estimateService = $this->getContainer()->get('caldera.criticalmass.statistic.rideestimate.track');
        $estimateService->addEstimate($track);
        $estimateService->calculateEstimates($ride);
    }

    protected function loadTrackProperties(Track $track)
    {
        /**
         * @var TrackReader $gr
         */
        $gr = $this->getContainer()->get('caldera.criticalmass.gps.trackreader');
        $gr->loadTrack($track);

        $track->setPoints($gr->countPoints());

        $track->setStartPoint(0);
        $track->setEndPoint($gr->countPoints() - 1);

        $track->setStartDateTime($gr->getStartDateTime());
        $track->setEndDateTime($gr->getEndDateTime());

        $track->setDistance($gr->calculateDistance());

        $track->setMd5Hash($gr->getMd5Hash());
    }
}