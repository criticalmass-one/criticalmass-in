<?php declare(strict_types=1);

namespace Tests\Criticalmass\Geo\FitService;

use App\Criticalmass\Geo\FitService\FitToGpxConverter;
use phpGPX\Models\GpxFile;
use PHPUnit\Framework\TestCase;

class FitToGpxConverterTest extends TestCase
{
    private const FIXTURE = __DIR__ . '/fixtures/road-cycling.fit';

    public function testConvertsFitFileToGpxWithTrackPoints(): void
    {
        $gpxFile = (new FitToGpxConverter())->convertFileToGpxFile(self::FIXTURE);

        self::assertInstanceOf(GpxFile::class, $gpxFile);
        self::assertCount(1, $gpxFile->tracks);

        $points = $gpxFile->tracks[0]->getPoints();
        self::assertGreaterThan(50, count($points), 'A road-cycling activity should contain many GPS points.');

        $first = $points[0];
        self::assertGreaterThan(-90.0, $first->latitude);
        self::assertLessThan(90.0, $first->latitude);
        self::assertGreaterThan(-180.0, $first->longitude);
        self::assertLessThan(180.0, $first->longitude);
        self::assertNotEquals(0.0, $first->latitude, 'Latitude must be a real, semicircle-converted degree value.');
        self::assertInstanceOf(\DateTimeInterface::class, $first->time);
    }

    public function testPointsAreChronological(): void
    {
        $points = (new FitToGpxConverter())->convertFileToGpxFile(self::FIXTURE)->tracks[0]->getPoints();

        $previousTimestamp = null;
        foreach ($points as $point) {
            $currentTimestamp = $point->time->getTimestamp();

            if ($previousTimestamp !== null) {
                self::assertGreaterThanOrEqual($previousTimestamp, $currentTimestamp, 'Track points must be chronological.');
            }

            $previousTimestamp = $currentTimestamp;
        }
    }

    public function testConvertFileToXmlStringProducesGpxTrackpoints(): void
    {
        $xml = (new FitToGpxConverter())->convertFileToXmlString(self::FIXTURE);

        self::assertStringContainsString('<gpx', $xml);
        self::assertStringContainsString('<trkpt', $xml);
    }

    public function testInvalidFileThrowsRuntimeException(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'notfit');
        file_put_contents($tmpFile, 'this is clearly not a FIT file');

        try {
            $this->expectException(\RuntimeException::class);
            (new FitToGpxConverter())->convertFileToGpxFile($tmpFile);
        } finally {
            @unlink($tmpFile);
        }
    }
}
