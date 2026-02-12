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
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'criticalmass:tracks:transform',
    description: 'Generate polylines for tracks that are missing them',
)]
class TracksTransformCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly GpxServiceInterface $gpxService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $tracks = $this->registry->getRepository(Track::class)->findAll();

        $missingTracks = array_filter($tracks, fn(Track $track) => $track->getTrackPolylines()->isEmpty());

        $io->info(sprintf('Found %d tracks without polylines', count($missingTracks)));

        if (count($missingTracks) === 0) {
            $io->success('All tracks already have polylines.');
            return Command::SUCCESS;
        }

        $em = $this->registry->getManager();
        $processed = 0;
        $skipped = 0;

        /** @var Track $track */
        foreach ($missingTracks as $track) {
            $io->write(sprintf('Track #%d: ', $track->getId()));

            if (!$track->getTrackFilename()) {
                $io->writeln('<comment>No GPX file, skipping</comment>');
                $skipped++;
                continue;
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

                $em->persist($track);
                $processed++;

                $io->writeln('<info>OK</info>');
            } catch (\Exception $e) {
                $io->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
                $skipped++;
            }
        }

        $em->flush();

        $io->success(sprintf('Processed %d tracks, skipped %d tracks.', $processed, $skipped));

        return Command::SUCCESS;
    }
}
