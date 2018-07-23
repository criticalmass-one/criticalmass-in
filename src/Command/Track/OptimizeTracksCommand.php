<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Criticalmass\Gps\DistanceCalculator\TrackDistanceCalculatorInterface;
use App\Entity\Track;
use App\Criticalmass\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OptimizeTracksCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var RangeLatLngListGenerator $rangeLatLngListGenerator */
    protected $rangeLatLngListGenerator;

    /** @var TrackDistanceCalculatorInterface $trackDistanceCalculator */
    protected $trackDistanceCalculator;

    /** @var EntityManager $manager */
    protected $manager;

    public function __construct(?string $name = null, RegistryInterface $registry, RangeLatLngListGenerator $rangeLatLngListGenerator, TrackDistanceCalculatorInterface $trackDistanceCalculator)
    {
        $this->registry = $registry;
        $this->rangeLatLngListGenerator = $rangeLatLngListGenerator;
        $this->trackDistanceCalculator = $trackDistanceCalculator;

        parent::__construct($name);
    }

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
            ->addOption('all');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repository = $this->registry->getRepository(Track::class);

        /** @var Track $track */
        if ($input->hasArgument('trackId') && $input->getArgument('trackId')) {
            $trackId = $input->getArgument('trackId');
            $track = $repository->find($trackId);

            $this->optimizeTrack($track);

            $output->writeln('Optimized Track #' . $track->getId());
        } elseif ($input->hasOption('all') && $input->getOption('all')) {
            $tracks = $repository->findAll();

            foreach ($tracks as $track) {
                $this->optimizeTrack($track);

                $output->writeln('Optimized Track #' . $track->getId());
                $output->writeln('Distance: ' . $track->getDistance());
            }
        }
    }

    protected function optimizeTrack(Track $track)
    {
        $list = $this->rangeLatLngListGenerator
            ->loadTrack($track)
            ->execute()
            ->getList();

        $track->setLatLngList($list);

        $this->trackDistanceCalculator->loadTrack($track);

        $track->setDistance($this->trackDistanceCalculator->calculate());

        $track->setUpdatedAt(new \DateTime());

        $this->registry->getManager()->persist($track);
        $this->registry->getManager()->flush();
    }
}
