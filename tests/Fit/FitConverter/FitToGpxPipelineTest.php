<?php declare(strict_types=1);

namespace Tests\Fit\FitConverter;

use App\Criticalmass\Fit\FitConverter\FitConverter;
use App\Criticalmass\Fit\FitParser\FitParser;
use App\Criticalmass\Geo\GpxService\GpxService;
use App\Entity\Track;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class FitToGpxPipelineTest extends TestCase
{
    private FitConverter $fitConverter;
    private GpxService $gpxService;
    private string $fixturesDir;

    protected function setUp(): void
    {
        $this->fixturesDir = __DIR__ . '/../../Fixtures/Fit';

        $fitParser = new FitParser();
        $this->gpxService = new GpxService(new ParameterBag([
            'upload_destination.track' => sys_get_temp_dir(),
        ]));

        $this->fitConverter = new FitConverter($fitParser, $this->gpxService);
    }

    private function getFirstFitFile(): string
    {
        $files = glob($this->fixturesDir . '/*.fit');

        return $files[0];
    }

    private function createTrackFromFitFile(string $fitFilePath): Track
    {
        $gpxString = $this->fitConverter->convertToGpxString($fitFilePath);

        $tmpFilename = uniqid('pipeline_test_', true) . '.gpx';
        $tmpPath = sys_get_temp_dir() . '/' . $tmpFilename;
        file_put_contents($tmpPath, $gpxString);

        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn($tmpFilename);
        $track->method('getStartPoint')->willReturn(0);

        $phpGpx = new \phpGPX\phpGPX();
        $gpxFile = $phpGpx->load($tmpPath);
        $pointCount = count($gpxFile->tracks[0]->getPoints());

        $track->method('getEndPoint')->willReturn($pointCount - 1);

        return $track;
    }

    public function testConvertedGpxProducesValidPolyline(): void
    {
        $track = $this->createTrackFromFitFile($this->getFirstFitFile());

        $polyline = $this->gpxService->generatePolyline($track);

        $this->assertIsString($polyline);
        $this->assertNotEmpty($polyline);
    }

    public function testConvertedGpxProducesValidReducedPolyline(): void
    {
        $track = $this->createTrackFromFitFile($this->getFirstFitFile());

        $reducedPolyline = $this->gpxService->generateReducedPolyline($track);

        $this->assertIsString($reducedPolyline);
        $this->assertNotEmpty($reducedPolyline);
    }

    public function testConvertedGpxProducesValidLatLngList(): void
    {
        $track = $this->createTrackFromFitFile($this->getFirstFitFile());

        $latLngList = $this->gpxService->generateLatLngList($track);

        $this->assertIsString($latLngList);

        $decoded = json_decode($latLngList, true);

        $this->assertIsArray($decoded);
        $this->assertNotEmpty($decoded);
    }

    public function testConvertedGpxCalculatesReasonableDistance(): void
    {
        $track = $this->createTrackFromFitFile($this->getFirstFitFile());

        $distance = $this->gpxService->calculateDistance($track);

        $this->assertGreaterThan(0, $distance);
        $this->assertLessThan(100, $distance);
    }

    public function testConvertedGpxHasValidStartEndDateTimes(): void
    {
        $track = $this->createTrackFromFitFile($this->getFirstFitFile());

        $startDateTime = $this->gpxService->getStartDateTime($track);
        $endDateTime = $this->gpxService->getEndDateTime($track);

        $this->assertInstanceOf(\DateTime::class, $startDateTime);
        $this->assertInstanceOf(\DateTime::class, $endDateTime);
        $this->assertGreaterThan($startDateTime, $endDateTime);
    }

    public function testConvertedGpxPolylineCanBeDecoded(): void
    {
        $track = $this->createTrackFromFitFile($this->getFirstFitFile());

        $polyline = $this->gpxService->generatePolyline($track);

        $decoded = @\Polyline::Decode($polyline);

        $this->assertIsArray($decoded);
        $this->assertGreaterThan(0, count($decoded));
    }

    protected function tearDown(): void
    {
        $files = glob(sys_get_temp_dir() . '/pipeline_test_*.gpx');
        foreach ($files as $file) {
            @unlink($file);
        }
    }
}
