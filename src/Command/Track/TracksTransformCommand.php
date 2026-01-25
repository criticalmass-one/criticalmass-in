<?php declare(strict_types=1);

namespace App\Command\Track;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\Track;
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

        $tracks = $this->registry->getRepository(Track::class)->findBy(['polyline' => null]);

        $io->info(sprintf('Found %d tracks without polyline', count($tracks)));

        if (count($tracks) === 0) {
            $io->success('All tracks already have polylines.');
            return Command::SUCCESS;
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

            try {
                $polyline = $this->gpxService->generatePolyline($track);
                $reducedPolyline = $this->gpxService->generateReducedPolyline($track);

                $track
                    ->setPolyline($polyline)
                    ->setReducedPolyline($reducedPolyline);

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
