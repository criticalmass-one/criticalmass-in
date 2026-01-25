<?php declare(strict_types=1);

namespace Tests\Geo\GpxService;

use App\Criticalmass\Geo\GpxService\GpxService;
use App\Entity\Track;
use phpGPX\Models\GpxFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class GpxServiceTest extends TestCase
{
    private GpxService $gpxService;
    private string $testGpxPath;

    protected function setUp(): void
    {
        $this->testGpxPath = __DIR__ . '/../../PhotoGps/data/braunschweig.gpx';

        $parameterBag = new ParameterBag([
            'upload_destination.track' => dirname($this->testGpxPath),
        ]);

        $this->gpxService = new GpxService($parameterBag);
    }

    public function testLoadFromFile(): void
    {
        $gpxFile = $this->gpxService->loadFromFile($this->testGpxPath);

        $this->assertInstanceOf(GpxFile::class, $gpxFile);
        $this->assertCount(1, $gpxFile->tracks);
        $this->assertGreaterThan(0, count($gpxFile->tracks[0]->getPoints()));
    }

    public function testLoadFromTrack(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');

        $gpxFile = $this->gpxService->loadFromTrack($track);

        $this->assertInstanceOf(GpxFile::class, $gpxFile);
        $this->assertCount(1, $gpxFile->tracks);
    }

    public function testGetPoints(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');

        $points = $this->gpxService->getPoints($track);

        $this->assertIsArray($points);
        $this->assertGreaterThan(0, count($points));
        $this->assertEquals(52.253218, $points[0]->latitude);
        $this->assertEquals(10.539028, $points[0]->longitude);
    }

    public function testGetPointsInRange(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(1);
        $track->method('getEndPoint')->willReturn(3);

        $points = $this->gpxService->getPointsInRange($track);

        // Should return points from index 1 to 3 (inclusive), so 3 points
        $this->assertCount(3, $points);
    }

    public function testGeneratePolyline(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(5);

        $polyline = $this->gpxService->generatePolyline($track);

        $this->assertIsString($polyline);
        $this->assertNotEmpty($polyline);
    }

    public function testGenerateReducedPolyline(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(10);

        $reducedPolyline = $this->gpxService->generateReducedPolyline($track);

        $this->assertIsString($reducedPolyline);
        $this->assertNotEmpty($reducedPolyline);
    }

    public function testGenerateLatLngList(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(20);

        $latLngList = $this->gpxService->generateLatLngList($track, 5);

        $this->assertIsString($latLngList);
        $this->assertStringStartsWith('[', $latLngList);
        $this->assertStringEndsWith(']', $latLngList);
    }

    public function testGenerateTimeLatLngList(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(20);

        $timeLatLngList = $this->gpxService->generateTimeLatLngList($track, 5);

        $this->assertIsString($timeLatLngList);
        $this->assertStringStartsWith('[', $timeLatLngList);
        // Should contain timestamps in format "2018-03-30T..."
        $this->assertStringContainsString('2018-03-30T', $timeLatLngList);
    }

    public function testCalculateDistance(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(10);

        $distance = $this->gpxService->calculateDistance($track);

        $this->assertIsFloat($distance);
        $this->assertGreaterThan(0, $distance);
    }

    public function testFindPointAtTime(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        // Search for a time within the track's range
        $searchTime = new \DateTime('2018-03-30T17:28:30Z');

        $point = $this->gpxService->findPointAtTime($track, $searchTime);

        $this->assertNotNull($point);
        $this->assertNotNull($point->latitude);
        $this->assertNotNull($point->longitude);
    }

    public function testGetStartDateTime(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(10);

        $startDateTime = $this->gpxService->getStartDateTime($track);

        $this->assertInstanceOf(\DateTime::class, $startDateTime);
        $this->assertEquals('2018-03-30', $startDateTime->format('Y-m-d'));
    }

    public function testGetEndDateTime(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(10);

        $endDateTime = $this->gpxService->getEndDateTime($track);

        $this->assertInstanceOf(\DateTime::class, $endDateTime);
        $this->assertEquals('2018-03-30', $endDateTime->format('Y-m-d'));
    }

    public function testCreateGpxFromStravaStream(): void
    {
        $latLngData = [
            [52.253218, 10.539028],
            [52.25283, 10.539004],
            [52.252803, 10.539035],
        ];
        $altitudeData = [76.4, 77.0, 77.0];
        $timeData = [0, 97, 100];
        $startDateTime = new \DateTime('2018-03-30T17:26:47Z');

        $gpxFile = $this->gpxService->createGpxFromStravaStream(
            $latLngData,
            $altitudeData,
            $timeData,
            $startDateTime
        );

        $this->assertInstanceOf(GpxFile::class, $gpxFile);
        $this->assertCount(1, $gpxFile->tracks);
        $this->assertCount(3, $gpxFile->tracks[0]->segments[0]->points);
        $this->assertEquals(52.253218, $gpxFile->tracks[0]->segments[0]->points[0]->latitude);
    }

    public function testToXmlString(): void
    {
        $gpxFile = $this->gpxService->loadFromFile($this->testGpxPath);
        $xmlString = $this->gpxService->toXmlString($gpxFile);

        $this->assertIsString($xmlString);
        $this->assertStringContainsString('<?xml', $xmlString);
        $this->assertStringContainsString('<gpx', $xmlString);
        $this->assertStringContainsString('<trk>', $xmlString);
    }
}
