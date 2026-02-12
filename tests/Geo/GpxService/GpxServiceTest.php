<?php declare(strict_types=1);

namespace Tests\Geo\GpxService;

use App\Criticalmass\Geo\GpxService\GpxService;
use App\Entity\Track;
use App\Enum\PolylineResolution;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Point;
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

    // ============================================
    // Loading Tests
    // ============================================

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

    public function testLoadFromFileHandlesInvalidFile(): void
    {
        // phpGPX triggers a warning for invalid files, suppress it for this test
        $result = @$this->gpxService->loadFromFile('/nonexistent/path/file.gpx');

        // phpGPX returns a GpxFile with empty tracks for invalid files
        $this->assertInstanceOf(GpxFile::class, $result);
    }

    // ============================================
    // Points Retrieval Tests
    // ============================================

    public function testGetPoints(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');

        $points = $this->gpxService->getPoints($track);

        $this->assertIsArray($points);
        $this->assertGreaterThan(0, count($points));
        $this->assertInstanceOf(Point::class, $points[0]);
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

    public function testGetPointsInRangeReturnsSubsetOfAllPoints(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(5);
        $track->method('getEndPoint')->willReturn(15);

        $allPoints = $this->gpxService->getPoints($track);
        $rangePoints = $this->gpxService->getPointsInRange($track);

        $this->assertCount(11, $rangePoints); // 5 to 15 inclusive = 11 points
        $this->assertLessThan(count($allPoints), count($rangePoints));
    }

    public function testGetPointsInRangeWithFullRange(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');

        $allPoints = $this->gpxService->getPoints($track);

        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(count($allPoints) - 1);

        $rangePoints = $this->gpxService->getPointsInRange($track);

        $this->assertCount(count($allPoints), $rangePoints);
    }

    // ============================================
    // Polyline Generation Tests
    // ============================================

    public function testGeneratePolylineAtResolutionFine(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(5);

        $polyline = $this->gpxService->generatePolylineAtResolution($track, PolylineResolution::FINE);

        $this->assertIsString($polyline);
        $this->assertNotEmpty($polyline);
    }

    public function testGeneratePolylineAtResolutionCoarse(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        $coarsePolyline = $this->gpxService->generatePolylineAtResolution($track, PolylineResolution::COARSE);

        $this->assertIsString($coarsePolyline);
        $this->assertNotEmpty($coarsePolyline);
    }

    public function testCoarsePolylineIsShorterOrEqualToFinePolyline(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        $finePolyline = $this->gpxService->generatePolylineAtResolution($track, PolylineResolution::FINE);
        $coarsePolyline = $this->gpxService->generatePolylineAtResolution($track, PolylineResolution::COARSE);

        // Coarse polyline should generally be shorter or equal in length
        $this->assertLessThanOrEqual(strlen($finePolyline), strlen($coarsePolyline) * 2);
    }

    public function testPolylineCanBeDecoded(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(5);

        $polyline = $this->gpxService->generatePolylineAtResolution($track, PolylineResolution::FINE);

        // Decode the polyline and verify it contains valid coordinates
        // Polyline::Decode returns a flat array [lat1, lng1, lat2, lng2, ...]
        $decoded = @\Polyline::Decode($polyline);

        $this->assertIsArray($decoded);
        $this->assertGreaterThan(0, count($decoded));

        // First point should be near Braunschweig (flat array: [lat, lng, lat, lng, ...])
        $this->assertEqualsWithDelta(52.25, $decoded[0], 0.1); // First latitude
        $this->assertEqualsWithDelta(10.54, $decoded[1], 0.1); // First longitude
    }

    // ============================================
    // LatLng List Generation Tests
    // ============================================

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

    public function testGenerateLatLngListIsValidJson(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(50);

        $latLngList = $this->gpxService->generateLatLngList($track, 10);

        $decoded = json_decode($latLngList, true);

        $this->assertIsArray($decoded);
        $this->assertGreaterThan(0, count($decoded));

        // Each entry should be [lat, lng]
        foreach ($decoded as $entry) {
            $this->assertIsArray($entry);
            $this->assertCount(2, $entry);
            $this->assertIsNumeric($entry[0]); // latitude
            $this->assertIsNumeric($entry[1]); // longitude
        }
    }

    public function testGenerateLatLngListSampleWidth(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(99); // 100 points

        $listWith10 = $this->gpxService->generateLatLngList($track, 10);
        $listWith20 = $this->gpxService->generateLatLngList($track, 20);

        $decoded10 = json_decode($listWith10, true);
        $decoded20 = json_decode($listWith20, true);

        // With sample width 10, we should have roughly 10 points
        // With sample width 20, we should have roughly 5 points
        $this->assertGreaterThan(count($decoded20), count($decoded10));
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

    public function testGenerateTimeLatLngListIsValidJson(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(50);

        $timeLatLngList = $this->gpxService->generateTimeLatLngList($track, 10);

        $decoded = json_decode($timeLatLngList, true);

        $this->assertIsArray($decoded);
        $this->assertGreaterThan(0, count($decoded));

        // Each entry should be ["timestamp", lat, lng]
        foreach ($decoded as $entry) {
            $this->assertIsArray($entry);
            $this->assertCount(3, $entry);
            $this->assertIsString($entry[0]); // timestamp
            $this->assertIsNumeric($entry[1]); // latitude
            $this->assertIsNumeric($entry[2]); // longitude
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $entry[0]);
        }
    }

    public function testGenerateSimpleLatLngListIncludesAllPoints(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(5);
        $track->method('getEndPoint')->willReturn(15);

        // generateSimpleLatLngList should use all points, not just the range
        $simpleList = $this->gpxService->generateSimpleLatLngList($track, 10);
        $rangeList = $this->gpxService->generateLatLngList($track, 10);

        $simpleDecoded = json_decode($simpleList, true);
        $rangeDecoded = json_decode($rangeList, true);

        // Simple list should have more points since it uses all points
        $this->assertGreaterThan(count($rangeDecoded), count($simpleDecoded));
    }

    // ============================================
    // Distance Calculation Tests
    // ============================================

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

    public function testCalculateDistanceReturnsKilometers(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        $distance = $this->gpxService->calculateDistance($track);

        // The Braunschweig track is a Critical Mass ride, should be a few km, not thousands
        $this->assertGreaterThan(0, $distance);
        $this->assertLessThan(100, $distance); // Should be less than 100km for a city ride
    }

    public function testCalculateDistanceWithSinglePoint(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(0); // Only one point

        $distance = $this->gpxService->calculateDistance($track);

        $this->assertEquals(0.0, $distance);
    }

    public function testCalculateDistanceIncreasesWithMorePoints(): void
    {
        $track1 = $this->createMock(Track::class);
        $track1->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track1->method('getStartPoint')->willReturn(0);
        $track1->method('getEndPoint')->willReturn(10);

        $track2 = $this->createMock(Track::class);
        $track2->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track2->method('getStartPoint')->willReturn(0);
        $track2->method('getEndPoint')->willReturn(50);

        $distance1 = $this->gpxService->calculateDistance($track1);
        $distance2 = $this->gpxService->calculateDistance($track2);

        $this->assertGreaterThan($distance1, $distance2);
    }

    // ============================================
    // Time-based Point Search Tests
    // ============================================

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
        $this->assertInstanceOf(Point::class, $point);
        $this->assertNotNull($point->latitude);
        $this->assertNotNull($point->longitude);
    }

    public function testFindPointAtTimeReturnsClosestPoint(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        // Search for exact time of first point
        $searchTime = new \DateTime('2018-03-30T17:26:47Z');

        $point = $this->gpxService->findPointAtTime($track, $searchTime);

        $this->assertNotNull($point);
        // Should be very close to the first point
        $this->assertEqualsWithDelta(52.253218, $point->latitude, 0.001);
        $this->assertEqualsWithDelta(10.539028, $point->longitude, 0.001);
    }

    public function testFindPointAtTimeBeforeTrackStart(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        // Search for time before track starts
        $searchTime = new \DateTime('2018-03-30T10:00:00Z');

        $point = $this->gpxService->findPointAtTime($track, $searchTime);

        // Should return the first point
        $this->assertNotNull($point);
    }

    public function testFindPointAtTimeAfterTrackEnd(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        // Search for time after track ends
        $searchTime = new \DateTime('2018-03-31T23:59:59Z');

        $point = $this->gpxService->findPointAtTime($track, $searchTime);

        // Should return the last point
        $this->assertNotNull($point);
    }

    // ============================================
    // DateTime Extraction Tests
    // ============================================

    public function testGetStartDateTime(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(10);

        $startDateTime = $this->gpxService->getStartDateTime($track);

        $this->assertInstanceOf(\DateTime::class, $startDateTime);
        $this->assertEquals('2018-03-30', $startDateTime->format('Y-m-d'));
        $this->assertEquals('17:26:47', $startDateTime->format('H:i:s'));
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

    public function testGetStartDateTimeRespectsRange(): void
    {
        $track1 = $this->createMock(Track::class);
        $track1->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track1->method('getStartPoint')->willReturn(0);
        $track1->method('getEndPoint')->willReturn(100);

        $track2 = $this->createMock(Track::class);
        $track2->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track2->method('getStartPoint')->willReturn(10);
        $track2->method('getEndPoint')->willReturn(100);

        $startDateTime1 = $this->gpxService->getStartDateTime($track1);
        $startDateTime2 = $this->gpxService->getStartDateTime($track2);

        // Start time with offset should be later
        $this->assertGreaterThan($startDateTime1, $startDateTime2);
    }

    public function testEndDateTimeIsAfterStartDateTime(): void
    {
        $track = $this->createMock(Track::class);
        $track->method('getTrackFilename')->willReturn('braunschweig.gpx');
        $track->method('getStartPoint')->willReturn(0);
        $track->method('getEndPoint')->willReturn(100);

        $startDateTime = $this->gpxService->getStartDateTime($track);
        $endDateTime = $this->gpxService->getEndDateTime($track);

        $this->assertGreaterThan($startDateTime, $endDateTime);
    }

    // ============================================
    // Strava Stream Conversion Tests
    // ============================================

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
        $this->assertCount(1, $gpxFile->tracks[0]->segments);
        $this->assertCount(3, $gpxFile->tracks[0]->segments[0]->points);
    }

    public function testCreateGpxFromStravaStreamPreservesCoordinates(): void
    {
        $latLngData = [
            [52.253218, 10.539028],
            [53.551086, 9.993682], // Hamburg
        ];
        $altitudeData = [76.4, 10.0];
        $timeData = [0, 3600];
        $startDateTime = new \DateTime('2018-03-30T17:26:47Z');

        $gpxFile = $this->gpxService->createGpxFromStravaStream(
            $latLngData,
            $altitudeData,
            $timeData,
            $startDateTime
        );

        $points = $gpxFile->tracks[0]->segments[0]->points;

        $this->assertEquals(52.253218, $points[0]->latitude);
        $this->assertEquals(10.539028, $points[0]->longitude);
        $this->assertEquals(53.551086, $points[1]->latitude);
        $this->assertEquals(9.993682, $points[1]->longitude);
    }

    public function testCreateGpxFromStravaStreamPreservesAltitude(): void
    {
        $latLngData = [
            [52.253218, 10.539028],
            [52.25283, 10.539004],
        ];
        $altitudeData = [76.4, 85.5];
        $timeData = [0, 60];
        $startDateTime = new \DateTime('2018-03-30T17:26:47Z');

        $gpxFile = $this->gpxService->createGpxFromStravaStream(
            $latLngData,
            $altitudeData,
            $timeData,
            $startDateTime
        );

        $points = $gpxFile->tracks[0]->segments[0]->points;

        $this->assertEquals(76.4, $points[0]->elevation);
        $this->assertEquals(85.5, $points[1]->elevation);
    }

    public function testCreateGpxFromStravaStreamCalculatesTimestamps(): void
    {
        $latLngData = [
            [52.253218, 10.539028],
            [52.25283, 10.539004],
        ];
        $altitudeData = [76.4, 77.0];
        $timeData = [0, 120]; // 2 minutes apart
        $startDateTime = new \DateTime('2018-03-30T17:00:00Z');

        $gpxFile = $this->gpxService->createGpxFromStravaStream(
            $latLngData,
            $altitudeData,
            $timeData,
            $startDateTime
        );

        $points = $gpxFile->tracks[0]->segments[0]->points;

        $this->assertEquals('2018-03-30 17:00:00', $points[0]->time->format('Y-m-d H:i:s'));
        $this->assertEquals('2018-03-30 17:02:00', $points[1]->time->format('Y-m-d H:i:s'));
    }

    // ============================================
    // XML Output Tests
    // ============================================

    public function testToXmlString(): void
    {
        $gpxFile = $this->gpxService->loadFromFile($this->testGpxPath);
        $xmlString = $this->gpxService->toXmlString($gpxFile);

        $this->assertIsString($xmlString);
        $this->assertStringContainsString('<?xml', $xmlString);
        $this->assertStringContainsString('<gpx', $xmlString);
        $this->assertStringContainsString('<trk>', $xmlString);
    }

    public function testToXmlStringIsValidXml(): void
    {
        $gpxFile = $this->gpxService->loadFromFile($this->testGpxPath);
        $xmlString = $this->gpxService->toXmlString($gpxFile);

        $xml = simplexml_load_string($xmlString);

        $this->assertNotFalse($xml);
        $this->assertInstanceOf(\SimpleXMLElement::class, $xml);
    }

    public function testToXmlStringContainsTrackpoints(): void
    {
        $gpxFile = $this->gpxService->loadFromFile($this->testGpxPath);
        $xmlString = $this->gpxService->toXmlString($gpxFile);

        $this->assertStringContainsString('<trkpt', $xmlString);
        $this->assertStringContainsString('lat=', $xmlString);
        $this->assertStringContainsString('lon=', $xmlString);
    }

    public function testCreatedGpxCanBeConvertedToXml(): void
    {
        $latLngData = [
            [52.253218, 10.539028],
            [52.25283, 10.539004],
        ];
        $altitudeData = [76.4, 77.0];
        $timeData = [0, 60];
        $startDateTime = new \DateTime('2018-03-30T17:26:47Z');

        $gpxFile = $this->gpxService->createGpxFromStravaStream(
            $latLngData,
            $altitudeData,
            $timeData,
            $startDateTime
        );

        $xmlString = $this->gpxService->toXmlString($gpxFile);

        $this->assertStringContainsString('52.253218', $xmlString);
        $this->assertStringContainsString('10.539028', $xmlString);
    }
}
