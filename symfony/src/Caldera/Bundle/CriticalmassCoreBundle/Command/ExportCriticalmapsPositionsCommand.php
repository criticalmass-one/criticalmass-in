<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter\GpxExporter;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxReader\TrackReader;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\SimpleLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CalderaBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Ticket;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CalderaBundle\Repository\CriticalmapsUserRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCriticalmapsPositionsCommand extends ContainerAwareCommand
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
            ->setName('criticalmass:positions:criticalmaps')
            ->setDescription('Export positions as gpx file')
            ->addArgument(
                'criticalmapsId',
                InputArgument::OPTIONAL,
                'Id of the critical maps user to export'
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

        /**
         * @var CriticalmapsUserRepository $repository
         */
        $repository = $this->doctrine->getRepository('CalderaCalderaBundle:CriticalmapsUser');

        if ($input->hasOption('all')) {
            $criticalmapsUsers = $repository->findNotExportedAssignedUsers();

            $counter = 0;

            foreach ($criticalmapsUsers as $criticalmapsUser) {
                $this->export($criticalmapsUser);

                ++$counter;

                if ($counter > 10) {
                    break;
                }
            }
        } else {
            /**
             * @var CriticalmapsUser $criticalmapsUser
             */
            $criticalmapsUser = $repository->find($input->getArgument('criticalmapsId'));

            if ($criticalmapsUser->getExported()) {
                $output->write('This user has already been exported.');
            } else {
                $this->export($criticalmapsUser);

                $message = 'Exported user '.$criticalmapsUser->getId();

                if ($criticalmapsUser->getRide()) {
                    $message .= ' ('.$criticalmapsUser->getRide()->getCity()->getCity().')';
                }

                $message.= ' from '.$criticalmapsUser->getStartDateTime()->format('d.m.Y H:i:s');
                $message.= ' to '.$criticalmapsUser->getEndDateTime()->format('d.m.Y H:i:s');

                $output->writeln($message);
            }
        }
    }

    protected function export(CriticalmapsUser $criticalmapsUser)
    {
        /**
         * @var GpxExporter $exporter
         */
        $exporter = $this->getContainer()->get('caldera.criticalmass.gps.gpxexporter');

        $exporter->setCriticalmapsUser($criticalmapsUser);

        $exporter->execute();

        $gpxContent = $exporter->getGpxContent();

        $criticalmapsUser->setExported(true);

        $filename = uniqid() . '.gpx';

        $fp = fopen('../web/tracks/' . $filename, 'w');
        fwrite($fp, $gpxContent);
        fclose($fp);

        $track = new Track();
        $track->setCriticalmapsUser($criticalmapsUser);
        $track->setTrackFilename($filename);
        $track->setUsername($criticalmapsUser->getIdentifier());
        $track->setRide($criticalmapsUser->getRide());

        $track->setStartDateTime($criticalmapsUser->getStartDateTime());
        $track->setEndDateTime($criticalmapsUser->getEndDateTime());

        $this->loadTrackProperties($track);
        $this->generateSimpleLatLngList($track);

        if ($track->getRide()) {
            $this->addRideEstimate($track, $track->getRide());
        }

        $em = $this->doctrine->getManager();
        $em->persist($track);
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