<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Entity\Track;
use App\Event\Track\TrackTrimmedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'criticalmass:tracks:optimize',
    description: 'Optimize tracks',
)]
class OptimizeTracksCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected EventDispatcherInterface $eventDispatcher)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'trackId',
                InputArgument::OPTIONAL,
                'Id of the track to optimize'
            )
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Optimize all tracks')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $repository = $this->registry->getRepository(Track::class);

        $table = new Table($output);
        $this->addHeaderToTable($table);

        $trackList = [];

        /** @var Track $track */
        if ($input->hasArgument('trackId') && $input->getArgument('trackId')) {
            $trackId = $input->getArgument('trackId');
            $track = $repository->find($trackId);

            $trackList = [$track];
        } elseif ($input->hasOption('all') && $input->getOption('all')) {
            $trackList = $repository->findAll();
        }

        foreach ($trackList as $track) {
            $this->optimizeTrack($track);

            $this->addTrackToTable($table, $track);
        }

        $table->render();

        return Command::SUCCESS;
    }

    protected function optimizeTrack(Track $track): void
    {
        // little trick: We just fire a TrackTrimmedEvent, which will lead to regeneration of all properties
        $this->eventDispatcher->dispatch(new TrackTrimmedEvent($track), TrackTrimmedEvent::NAME);
    }

    protected function addHeaderToTable(Table $table): void
    {
        $table->setHeaders([
            'Id',
            'Username',
            'City',
            'Date',
            'Distance',
            'Points',
            'Startpoint',
            'Endpoint',
            'Starttime',
            'Endtime',
            'Strava id',
            'Enabled',
            'Deleted',
        ]);
    }

    protected function addTrackToTable(Table $table, Track $track): void
    {
        $table->addRow([
            $track->getId(),
            $track->getUser()->getUsername(),
            $track->getRide()->getCity()->getCity(),
            $track->getRide()->getDateTime()->format('Y-m-d'),
            $track->getDistance(),
            $track->getPoints(),
            $track->getStartPoint(),
            $track->getEndPoint(),
            $track->getStartDateTime()->format('H:i'),
            $track->getEndDateTime()->format('H:i'),
            $track->getStravaActivityId(),
            $track->getEnabled(),
            $track->getDeleted(),
        ]);
    }
}
