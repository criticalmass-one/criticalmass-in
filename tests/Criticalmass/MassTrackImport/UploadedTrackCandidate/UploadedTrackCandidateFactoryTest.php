<?php declare(strict_types=1);

namespace Tests\Criticalmass\MassTrackImport\UploadedTrackCandidate;

use App\Criticalmass\Geo\FitService\FitToGpxConverter;
use App\Criticalmass\Geo\GpxService\GpxService;
use App\Criticalmass\MassTrackImport\UploadedTrackCandidate\UploadedTrackCandidateFactory;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UploadedTrackCandidateFactoryTest extends TestCase
{
    private const FIT_FIXTURE = __DIR__ . '/../../../Criticalmass/Geo/FitService/fixtures/road-cycling.fit';

    private function factory(): UploadedTrackCandidateFactory
    {
        return new UploadedTrackCandidateFactory(
            new GpxService($this->createMock(ParameterBagInterface::class)),
            new FitToGpxConverter(),
        );
    }

    public function testBuildsUploadCandidateFromFitFile(): void
    {
        $parsed = $this->factory()->createFromUpload(self::FIT_FIXTURE, 'road-cycling.fit', $this->createMock(User::class));

        $candidate = $parsed->getCandidate();

        self::assertSame(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD, $candidate->getSource());
        self::assertSame('Ride', $candidate->getType(), 'Upload candidates use a neutral "Ride" type so they are not disqualified.');
        self::assertSame('road-cycling.fit', $candidate->getOriginalName());
        self::assertSame('road-cycling.fit', $candidate->getName());
        self::assertNull($candidate->getActivityId(), 'Upload candidates have no Strava activity id.');
        self::assertSame(40, strlen((string) $candidate->getFileHash()), 'fileHash should be a SHA-1 hex string.');
        self::assertGreaterThan(0, $candidate->getStartDateTime()->getTimestamp());
        self::assertGreaterThan(0, $candidate->getElapsedTime());
        self::assertGreaterThan(0, $candidate->getDistance());
        self::assertNotSame(0.0, $candidate->getStartLatitude());
        self::assertNotSame(0.0, $candidate->getStartLongitude());
        self::assertNotEmpty($candidate->getPolyline());

        self::assertStringContainsString('<trkpt', $parsed->getGpxXml(), 'A normalised GPX must be produced for storage.');
    }

    public function testBuildsUploadCandidateFromGpxFile(): void
    {
        $gpxFile = $this->generateGpxFile(53.55, 9.99, '2024-06-01T19:00:00Z', 60);

        try {
            $parsed = $this->factory()->createFromUpload($gpxFile, 'my-ride.gpx', $this->createMock(User::class));
        } finally {
            @unlink($gpxFile);
        }

        $candidate = $parsed->getCandidate();

        self::assertSame(TrackImportCandidate::CANDIDATE_SOURCE_UPLOAD, $candidate->getSource());
        self::assertSame('my-ride.gpx', $candidate->getName());
        // 60 points, 2 minutes apart → 59 * 120 = 7080 seconds
        self::assertSame(7080, $candidate->getElapsedTime());
        self::assertEqualsWithDelta(53.55, $candidate->getStartLatitude(), 0.05);
    }

    public function testBuildsUploadCandidateAcrossSeveralTrackElements(): void
    {
        $gpxFile = $this->generateMultiTrackGpxFile();

        try {
            $parsed = $this->factory()->createFromUpload($gpxFile, 'two-parts.gpx', $this->createMock(User::class));
        } finally {
            @unlink($gpxFile);
        }

        $candidate = $parsed->getCandidate();

        // Erster Punkt 10:00, letzter Punkt 11:02 — der zweite <trk> zählt also mit.
        self::assertSame(3720, $candidate->getElapsedTime(), 'The candidate must span every <trk> element, not just the first one.');
        self::assertEqualsWithDelta(52.0, $candidate->getStartLatitude(), 0.01);
        self::assertEqualsWithDelta(52.502, $candidate->getEndLatitude(), 0.01, 'The last point of the last track ends the ride.');
    }

    public function testUnsupportedFileTypeThrows(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->factory()->createFromUpload(self::FIT_FIXTURE, 'notes.txt', $this->createMock(User::class));
    }

    private function generateMultiTrackGpxFile(): string
    {
        $gpx = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<gpx version="1.1" creator="test">' . "\n";

        foreach ([[52.0, 10.0, '2026-09-01T10:00:00Z'], [52.5, 10.5, '2026-09-01T11:00:00Z']] as [$lat, $lon, $startTime]) {
            $dateTime = new \DateTime($startTime);
            $gpx .= '<trk><trkseg>' . "\n";

            for ($i = 0; $i < 3; $i++) {
                $gpx .= sprintf(
                    '<trkpt lat="%.6f" lon="%.6f"><time>%s</time></trkpt>' . "\n",
                    $lat + $i * 0.001,
                    $lon + $i * 0.001,
                    $dateTime->format('Y-m-d\TH:i:s\Z'),
                );
                $dateTime->modify('+1 minute');
            }

            $gpx .= '</trkseg></trk>' . "\n";
        }

        $gpx .= '</gpx>';

        $tmpFile = tempnam(sys_get_temp_dir(), 'gpx_factory_') . '.gpx';
        file_put_contents($tmpFile, $gpx);

        return $tmpFile;
    }

    private function generateGpxFile(float $startLat, float $startLon, string $startTime, int $pointCount): string
    {
        $gpx = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<gpx version="1.1" creator="test"><trk><trkseg>' . "\n";

        $dateTime = new \DateTime($startTime);

        for ($i = 0; $i < $pointCount; $i++) {
            $angle = 2 * M_PI * $i / $pointCount;
            $gpx .= sprintf(
                '<trkpt lat="%.6f" lon="%.6f"><time>%s</time></trkpt>' . "\n",
                $startLat + sin($angle) * 0.01,
                $startLon + cos($angle) * 0.01,
                $dateTime->format('Y-m-d\TH:i:s\Z'),
            );
            $dateTime->modify('+2 minutes');
        }

        $gpx .= '</trkseg></trk></gpx>';

        $tmpFile = tempnam(sys_get_temp_dir(), 'gpx_factory_') . '.gpx';
        file_put_contents($tmpFile, $gpx);

        return $tmpFile;
    }
}
