<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Criticalmass\Geo\TrackPolylineHandler\TrackPolylineHandlerInterface;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'criticalmass:track:reduce-polyline',
    description: 'Reduce polylines of tracks',
)]
class TrackReducePolylineCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected TrackPolylineHandlerInterface $trackPolylineHandler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('all', 'a', InputOption::VALUE_OPTIONAL, 'Generate polylines for all tracks')
            ->addArgument('trackId', InputArgument::OPTIONAL, 'Id of the track to reduce polyline');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($input->hasOption('all') && $input->getOption('all')) {
            $tracks = $this->registry->getRepository(Track::class)->findAll();
        } elseif ($input->hasArgument('trackId') && $trackId = $input->getArgument('trackId')) {
            $tracks = [$this->registry->getRepository(Track::class)->find($trackId)];
        } else {
            $output->writeln('No tracks selected to refresh.');

            return Command::FAILURE;
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

        return Command::SUCCESS;
    }
}
