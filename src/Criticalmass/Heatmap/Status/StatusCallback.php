<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Status;

use App\Criticalmass\Heatmap\Generator\HeatmapGenerator;
use App\Entity\Track;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class StatusCallback
{
    /** @var OutputInterface */
    protected $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function onTrack(Status $status, Track $track): void
    {
        $this->output->writeln(sprintf('Now painting track #%d by %s', $track->getId(), $track->getUser()->getUsername()));
    }

    public function onZoomLevel(Status $status): void
    {
        $this->output->writeln(sprintf('Now painting zoomlevel <info>%d</info> of <info>%d</info>', $status->getZoomLevel(), HeatmapGenerator::MAX_ZOOMLEVEL));
        $this->output->writeln(sprintf('Memory usage: <info>%.2f</info> mb', $status->getMemoryUsage() / 1024 / 1024));
    }

    public function onTile(Status $status): void
    {
        $progressBar = new ProgressBar($this->output, $status->getMaxTiles());

        $progressBar->setProgress($status->getPaintedTiles());
    }
}