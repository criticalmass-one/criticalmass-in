<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Criticalmass\Geo\TrackPolylineHandler\TrackPolylineHandlerInterface;
use App\Entity\Track;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TrackReducePolylineCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var TrackPolylineHandlerInterface $trackPolylineHandler */
    protected $trackPolylineHandler;

    public function __construct(?string $name = null, RegistryInterface $registry, TrackPolylineHandlerInterface $trackPolylineHandler)
    {
        $this->trackPolylineHandler = $trackPolylineHandler;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:track:reduce-polyline')
            ->setDescription('')
            ->addOption('all', 'a', InputOption::VALUE_OPTIONAL, 'Generate polylines for all tracks')
            ->addArgument('trackId', InputArgument::OPTIONAL, 'Id of the track to reduce polyline');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($input->hasOption('all') && $input->getOption('all')) {
            $tracks = $this->registry->getRepository(Track::class)->findAll();
        } elseif ($input->hasArgument('trackId') && $trackId = $input->getArgument('trackId')) {
            $tracks = [$this->registry->getRepository(Track::class)->find($trackId)];
        } else {
            $output->writeln('No tracks selected to refresh.');
            return;
        }

        $progressBar = new ProgressBar($output, count($tracks));

        $table = new Table($output);
        $table->setHeaders([
            'Track Id',
            'Username',
            'DateTime',
            'City',
            'Ride Date Time',
            'Reduced Polyline',
        ]);

        /** @var Track $track */
        foreach ($tracks as $track) {
            $track = $this->trackPolylineHandler->handleTrack($track);

            $progressBar->advance();
            $table->addRow([
                $track->getId(),
                $track->getUsername(),
                $track->getCreationDateTime()->format('Y-m-d H:i:s'),
                ($track->getRide() && $track->getRide()->getCity() ? $track->getRide()->getCity()->getCity() : ''),
                ($track->getRide() ? $track->getRide()->getDateTime()->format('Y-m-d H:i') : ''),
                $track->getReducedPolyline(),
            ]);
        }

        $this->registry->getManager()->flush();

        $progressBar->finish();
        $table->render();
    }
}
