<?php declare(strict_types=1);

namespace App\Command\Heatmap;

use App\Criticalmass\Heatmap\Generator\HeatmapGenerator;
use App\Criticalmass\Heatmap\Generator\Status;
use App\Entity\Heatmap;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command
{
    protected const DEFAULT_TRACKS = 3;

    protected static $defaultName = 'criticalmass:heatmap:generate';

    /** @var HeatmapGenerator $heatmapGenerator */
    protected $heatmapGenerator;

    /** @var RegistryInterface $registry */
    protected $registry;

    protected function configure()
    {
        $this
            ->setDescription('Generate heatmap')
            ->addArgument('identifier', InputArgument::REQUIRED, 'Heatmap identifier')
            ->addOption('max-tracks', 'mt', InputOption::VALUE_REQUIRED, 'Number of tracks to paint per call', self::DEFAULT_TRACKS);
    }

    public function __construct(string $name = null, HeatmapGenerator $heatmapGenerator, RegistryInterface $registry)
    {
        $this->heatmapGenerator = $heatmapGenerator;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $heatmap = $this->registry->getRepository(Heatmap::class)->findOneByIdentifier($input->getArgument('identifier'));

        $this->heatmapGenerator
            ->setHeatmap($heatmap)
            ->setMaxPaintedTracks((int) $input->getOption('max-tracks'))
            ->setCallback(function (Status $status) use ($output) {
                $output->writeln(sprintf('Current zoom level: %d', $status->getZoomLevel()));
                $output->writeln(sprintf('Current tiles: %d / %d', $status->getPaintedTiles(), $status->getMaxTiles()));
                $output->writeln(sprintf('Current tracks: %d / %d', $status->getPaintedTracks(), $status->getMaxTracks()));
            })
            ->generate();
    }
}