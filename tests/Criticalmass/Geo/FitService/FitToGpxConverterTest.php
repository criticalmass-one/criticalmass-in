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

    /**
     * The whole "normalise FIT to GPX on ingest" approach only works if the produced GPX
     * passes App\Criticalmass\UploadValidator\TrackValidator. This replicates its exact
     * structural checks against the converter output (DB-free).
     */
    public function testConvertedGpxSatisfiesTrackValidatorExpectations(): void
    {
        $xml = (new FitToGpxConverter())->convertFileToXmlString(self::FIXTURE);

        $simpleXml = new \SimpleXMLElement($xml, LIBXML_NONET | LIBXML_NOENT);

        self::assertNotEmpty($simpleXml->trk->trkseg->trkpt, 'Converted GPX must expose trk/trkseg/trkpt.');

        $pointCount = 0;
        foreach ($simpleXml->trk->trkseg as $segment) {
            foreach ($segment->trkpt as $point) {
                ++$pointCount;
                self::assertMatchesRegularExpression('/^([-]?)([0-9]{1,3})\.([0-9]*)$/', (string) $point['lat']);
                self::assertMatchesRegularExpression('/^([-]?)([0-9]{1,3})\.([0-9]*)$/', (string) $point['lon']);
                self::assertNotEmpty((string) $point->time, 'Each track point must carry a <time> element.');
            }
        }

        self::assertGreaterThan(50, $pointCount, 'TrackValidator requires more than 50 points.');
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
