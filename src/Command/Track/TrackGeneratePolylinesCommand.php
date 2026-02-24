<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\Track;
use App\Entity\TrackPolyline;
use App\Enum\PolylineResolution;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'criticalmass:track:generate-polylines',
    description: 'Generate TrackPolyline entities for all resolutions',
)]
class TrackGeneratePolylinesCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly GpxServiceInterface $gpxService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Generate polylines for all tracks')
            ->addOption('track-id', 't', InputOption::VALUE_REQUIRED, 'Generate polylines for a specific track')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Regenerate existing polylines');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');
        $trackId = $input->getOption('track-id');
        $all = $input->getOption('all');

        $trackRepository = $this->registry->getRepository(Track::class);

        if ($trackId) {
            $track = $trackRepository->find($trackId);

            if (!$track) {
                $io->error(sprintf('Track #%s not found.', $trackId));
                return Command::FAILURE;
            }

            $tracks = [$track];
        } elseif ($all) {
            $tracks = $trackRepository->findAll();
        } else {
            $io->error('Please specify --all or --track-id=N');
            return Command::FAILURE;
        }

        $em = $this->registry->getManager();
        $processed = 0;
        $skipped = 0;

        /** @var Track $track */
        foreach ($tracks as $track) {
            $io->write(sprintf('Track #%d: ', $track->getId()));

            if (!$track->getTrackFilename()) {
                $io->writeln('<comment>No GPX file, skipping</comment>');
                $skipped++;
                continue;
            }

            $existing = !$track->getTrackPolylines()->isEmpty();

            if ($existing) {
                if (!$force) {
                    $io->writeln('<comment>Already has polylines, skipping (use --force to regenerate)</comment>');
                    $skipped++;
                    continue;
                }

                foreach ($track->getTrackPolylines()->toArray() as $polyline) {
                    $track->removeTrackPolyline($polyline);
                }
            }

            try {
                foreach (PolylineResolution::cases() as $resolution) {
                    $polylineString = $this->gpxService->generatePolylineAtResolution($track, $resolution);
                    $numPoints = (int) (count(\Polyline::Decode($polylineString)) / 2);

                    $trackPolyline = new TrackPolyline();
                    $trackPolyline
                        ->setResolution($resolution)
                        ->setPolyline($polylineString)
                        ->setNumPoints($numPoints);

                    $track->addTrackPolyline($trackPolyline);
                }

                $processed++;
                $io->writeln('<info>OK</info>');
            } catch (\Exception $e) {
                $io->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
                $skipped++;
            }

            if ($processed % 50 === 0) {
                $em->flush();
            }
        }

        $em->flush();

        $io->success(sprintf('Processed %d tracks, skipped %d tracks.', $processed, $skipped));

        return Command::SUCCESS;
    }
}
