<?php declare(strict_types=1);

namespace Tests\Command\Track;

use App\Command\Track\TrackGeneratePolylinesCommand;
use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\Track;
use App\Entity\TrackPolyline;
use App\Enum\PolylineResolution;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\Support\EntityIdHelper;

final class TrackGeneratePolylinesCommandTest extends TestCase
{
    private MockObject&TrackRepository $repository;
    private MockObject&GpxServiceInterface $gpxService;
    private MockObject&ObjectManager $manager;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(TrackRepository::class);
        $this->gpxService = $this->createMock(GpxServiceInterface::class);
        $this->manager = $this->createMock(ObjectManager::class);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Track::class)->willReturn($this->repository);
        $registry->method('getManager')->willReturn($this->manager);

        $application = new Application();
        $application->addCommand(new TrackGeneratePolylinesCommand($registry, $this->gpxService));

        $this->tester = new CommandTester($application->find('criticalmass:track:generate-polylines'));
    }

    private function track(int $id, ?string $filename = 'tracks/ride.gpx'): Track
    {
        $track = new Track();
        $track->setTrackFilename($filename);
        EntityIdHelper::setId($track, $id);

        return $track;
    }

    #[Test]
    public function requiresEitherAllOrTrackId(): void
    {
        $this->tester->execute([]);

        self::assertSame(1, $this->tester->getStatusCode());
        self::assertStringContainsString('Please specify --all or --track-id=N', $this->tester->getDisplay());
    }

    #[Test]
    public function unknownTrackIdFails(): void
    {
        $this->repository->method('find')->with('999')->willReturn(null);

        $this->tester->execute(['--track-id' => '999']);

        self::assertSame(1, $this->tester->getStatusCode());
        self::assertStringContainsString('Track #999 not found', $this->tester->getDisplay());
    }

    #[Test]
    public function generatesOnePolylinePerResolution(): void
    {
        $track = $this->track(5);
        $this->repository->method('find')->willReturn($track);

        // An empty polyline decodes to zero points without touching the (deprecated) decoder internals.
        $this->gpxService->expects(self::exactly(count(PolylineResolution::cases())))
            ->method('generatePolylineAtResolution')
            ->with($track, self::isInstanceOf(PolylineResolution::class))
            ->willReturn('');
        $this->manager->expects(self::atLeastOnce())->method('flush');

        $this->tester->execute(['--track-id' => '5']);

        self::assertSame(0, $this->tester->getStatusCode());
        self::assertCount(3, $track->getTrackPolylines());

        $resolutions = array_map(
            static fn (TrackPolyline $polyline): PolylineResolution => $polyline->getResolution(),
            $track->getTrackPolylines()->toArray()
        );
        self::assertEqualsCanonicalizing(PolylineResolution::cases(), $resolutions);
        self::assertStringContainsString('Processed 1 tracks, skipped 0 tracks', $this->tester->getDisplay());
    }

    #[Test]
    public function tracksWithoutGpxFileAreSkipped(): void
    {
        $this->repository->method('findAll')->willReturn([$this->track(1, null)]);
        $this->gpxService->expects(self::never())->method('generatePolylineAtResolution');

        $this->tester->execute(['--all' => true]);

        self::assertSame(0, $this->tester->getStatusCode());
        self::assertStringContainsString('No GPX file, skipping', $this->tester->getDisplay());
        self::assertStringContainsString('Processed 0 tracks, skipped 1 tracks', $this->tester->getDisplay());
    }

    #[Test]
    public function existingPolylinesAreKeptUnlessForced(): void
    {
        $track = $this->track(2);
        $existing = (new TrackPolyline())->setResolution(PolylineResolution::COARSE)->setPolyline('abc')->setNumPoints(1);
        $track->addTrackPolyline($existing);

        $this->repository->method('findAll')->willReturn([$track]);
        $this->gpxService->expects(self::never())->method('generatePolylineAtResolution');

        $this->tester->execute(['--all' => true]);

        self::assertStringContainsString('Already has polylines, skipping', $this->tester->getDisplay());
        self::assertSame([$existing], $track->getTrackPolylines()->toArray());
    }

    #[Test]
    public function forceRegeneratesExistingPolylines(): void
    {
        $track = $this->track(2);
        $track->addTrackPolyline((new TrackPolyline())->setResolution(PolylineResolution::COARSE)->setPolyline('abc')->setNumPoints(1));

        $this->repository->method('findAll')->willReturn([$track]);
        $this->gpxService->method('generatePolylineAtResolution')->willReturn('');

        $this->tester->execute(['--all' => true, '--force' => true]);

        self::assertSame(0, $this->tester->getStatusCode());
        self::assertCount(3, $track->getTrackPolylines());
        self::assertStringContainsString('Processed 1 tracks, skipped 0 tracks', $this->tester->getDisplay());
    }

    #[Test]
    public function generatorErrorsAreReportedPerTrackAndDoNotAbort(): void
    {
        $broken = $this->track(1);
        $fine = $this->track(2);

        $this->repository->method('findAll')->willReturn([$broken, $fine]);
        $this->gpxService->method('generatePolylineAtResolution')->willReturnCallback(
            static function (Track $track): string {
                if (1 === $track->getId()) {
                    throw new \RuntimeException('corrupt gpx');
                }

                return '';
            }
        );

        $this->tester->execute(['--all' => true]);

        self::assertSame(0, $this->tester->getStatusCode());
        self::assertStringContainsString('Error: corrupt gpx', $this->tester->getDisplay());
        self::assertStringContainsString('Processed 1 tracks, skipped 1 tracks', $this->tester->getDisplay());
    }
}
