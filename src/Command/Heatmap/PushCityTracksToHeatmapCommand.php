<?php declare(strict_types=1);

namespace App\Command\Heatmap;

use App\Entity\City;
use App\Entity\Track;
use App\Repository\CityRepository;
use App\Repository\TrackRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'criticalmass:heatmap:push-city-tracks',
    description: 'Push all tracks of a city as high-resolution polylines to the heatmap API',
)]
class PushCityTracksToHeatmapCommand extends Command
{
    private const HEATMAP_API_BASE_URL = 'https://maps.jetzt';
    private const BATCH_SIZE = 50;

    public function __construct(
        private readonly CityRepository $cityRepository,
        private readonly TrackRepository $trackRepository,
        private readonly HttpClientInterface $httpClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('citySlug', InputArgument::REQUIRED, 'Slug of the city');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string $slug */
        $slug = $input->getArgument('citySlug');
        $city = $this->findCityBySlug($slug);

        if (!$city) {
            $io->error(sprintf('City with slug "%s" not found.', $slug));
            return Command::FAILURE;
        }

        $io->title(sprintf('Pushing tracks for city "%s"', $city->getCity() ?? $slug));

        $tracks = $this->trackRepository->findByCity($city);

        /** @var list<string> $polylines */
        $polylines = [];

        /** @var Track $track */
        foreach ($tracks as $track) {
            $polyline = $track->getPolyline();

            if ($polyline) {
                $polylines[] = $polyline;
            }
        }

        if (count($polylines) === 0) {
            $io->warning('No tracks with polylines found for this city.');
            return Command::SUCCESS;
        }

        $io->info(sprintf('Found %d tracks with polylines.', count($polylines)));

        $identifier = sprintf('criticalmass-%s', $slug);

        if (!$this->createHeatmap($io, $identifier)) {
            return Command::FAILURE;
        }

        if (!$this->pushPolylines($io, $identifier, $polylines)) {
            return Command::FAILURE;
        }

        $io->success(sprintf('Pushed %d polylines to heatmap "%s".', count($polylines), $identifier));

        return Command::SUCCESS;
    }

    private function findCityBySlug(string $slug): ?City
    {
        $cities = $this->cityRepository->findCitiesBySlugList([$slug]);

        return $cities[0] ?? null;
    }

    private function createHeatmap(SymfonyStyle $io, string $identifier): bool
    {
        try {
            $response = $this->httpClient->request('POST', self::HEATMAP_API_BASE_URL . '/api/heatmaps', [
                'json' => [
                    'identifier' => $identifier,
                ],
            ]);

            $statusCode = $response->getStatusCode();

            if ($statusCode === 201) {
                $io->info(sprintf('Created heatmap "%s".', $identifier));
                return true;
            }

            if ($statusCode === 409) {
                $io->info(sprintf('Heatmap "%s" already exists, adding polylines.', $identifier));
                return true;
            }

            $io->error(sprintf('Failed to create heatmap: HTTP %d', $statusCode));
            return false;
        } catch (\Exception $e) {
            $io->error(sprintf('Failed to create heatmap: %s', $e->getMessage()));
            return false;
        }
    }

    /** @param list<string> $polylines */
    private function pushPolylines(SymfonyStyle $io, string $identifier, array $polylines): bool
    {
        $batches = array_chunk($polylines, self::BATCH_SIZE);
        $totalPushed = 0;

        $io->progressStart(count($polylines));

        foreach ($batches as $batch) {
            try {
                $response = $this->httpClient->request('POST', sprintf('%s/api/heatmaps/%s/polylines', self::HEATMAP_API_BASE_URL, $identifier), [
                    'json' => [
                        'polylines' => $batch,
                    ],
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode !== 201) {
                    $io->newLine();
                    $io->error(sprintf('Failed to push polylines: HTTP %d', $statusCode));
                    return false;
                }

                $totalPushed += count($batch);
                $io->progressAdvance(count($batch));
            } catch (\Exception $e) {
                $io->newLine();
                $io->error(sprintf('Failed to push polylines: %s', $e->getMessage()));
                return false;
            }
        }

        $io->progressFinish();

        return true;
    }
}
