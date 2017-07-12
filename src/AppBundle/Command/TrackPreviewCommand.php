<?php

namespace AppBundle\Command;

use AppBundle\Entity\Track;
use AppBundle\Gps\TrackPolyline\TrackPolyline;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TrackPreviewCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:tracks:generate-preview')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $trackList = $this->getContainer()->get('doctrine')->getRepository('AppBundle:Track')->findAll();

        $em = $this->getContainer()->get('doctrine')->getManager();

        /** @var TrackPolyline $trackPolyline */
        $trackPolyline = $this->getContainer()->get('caldera.criticalmass.gps.polyline.track');

        /**
         * @var Track $track
         */
        foreach ($trackList as $track) {
            try {
                $polyline = $trackPolyline
                    ->loadTrack($track)
                    ->generatePreviewPolyline()
                    ->getPolyline();

                $track->setPreviewPolyline($polyline);

                $output->writeln(sprintf('Track <info>#%d</info>: <comment>%s</comment>', $track->getId(), $track->getPreviewPolyline()));
            } catch (\Exception $e) {
                $output->writeln(sprintf('Track <info>#%d</info>: %s', $track->getId(), $e->getMessage()));
            }
        }

        $em->flush();
    }
}