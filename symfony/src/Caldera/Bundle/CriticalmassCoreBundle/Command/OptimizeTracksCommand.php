<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class OptimizeTracksCommand extends ContainerAwareCommand
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
            ->setName('criticalmass:tracks:optimize')
            ->setDescription('Regenerate LatLng Tracks')
            ->addArgument(
                'trackId',
                InputArgument::OPTIONAL,
                'Id of the Track to optimize'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->generator = $this->getContainer()->get('caldera.criticalmass.gps.latlnglistgenerator.range');
        $this->manager = $this->doctrine->getManager();

        $repository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Track');

        /**
         * @var Track $track
         */
        if ($input->hasArgument('trackId') && $input->getArgument('trackId')) {
            $trackId = $input->getArgument('trackId');
            $track = $repository->find($trackId);

            $this->optimizeTrack($track);

            $output->writeln('Optimized Track #'.$track->getId());
        } else {
            $tracks = $repository->findAll();

            foreach ($tracks as $track) {
                $this->optimizeTrack($track);

                $output->writeln('Optimized Track #'.$track->getId());
            }
        }
    }

    protected function optimizeTrack(Track $track)
    {
        $list = $this->generator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);

        $this->manager->persist($track);
        $this->manager->flush();
    }
}