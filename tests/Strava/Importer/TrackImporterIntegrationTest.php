<?php declare(strict_types=1);

namespace Tests\Strava\Importer;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Criticalmass\Strava\Importer\TrackImporter;
use App\Criticalmass\Strava\Stream\Stream;
use App\Criticalmass\Strava\Stream\StreamList;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use phpGPX\Models\GpxFile;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Test class that allows testing protected methods by extending TrackImporter
 * and making the API mockable
 */
class TestableTrackImporter extends TrackImporter
{
    private ?\stdClass $mockActivity = null;
    private ?StreamList $mockStreamList = null;

    public function setMockActivity(\stdClass $activity): void
    {
        $this->mockActivity = $activity;
    }

    public function setMockStreamList(StreamList $streamList): void
    {
        $this->mockStreamList = $streamList;
    }

    protected function getActivity(bool $allEfforts = true): \stdClass
    {
        if ($this->mockActivity) {
            return $this->mockActivity;
        }
        return parent::getActivity($allEfforts);
    }

    protected function getActivityStreamList(): StreamList
    {
        if ($this->mockStreamList) {
            return $this->mockStreamList;
        }
        return parent::getActivityStreamList();
    }
}


class TrackImporterIntegrationTest extends TestCase
{
    private GpxServiceInterface $gpxService;
    private RequestStack $requestStack;
    private ManagerRegistry $registry;
    private UploadFakerInterface $uploadFaker;
    private SessionInterface $session;
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        $this->gpxService = $this->createMock(GpxServiceInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->uploadFaker = $this->createMock(UploadFakerInterface::class);
        $this->session = $this->createMock(SessionInterface::class);
        $this->entityManager = $this->createMock(EntityManager::class);

        // Setup session with token
        $token = new StravaTokenStorage('test-access-token', 'test-refresh-token', time() + 3600);
        $this->session->method('get')
            ->with('strava_token')
            ->willReturn($token);

        $this->requestStack->method('getSession')
            ->willReturn($this->session);

        $this->registry->method('getManager')
            ->willReturn($this->entityManager);
    }

    public function testImportTrackCreatesTrackEntity(): void
    {
        $importer = $this->createTestableTrackImporter();

        $user = new User();
        $user->setUsername('testuser');

        $ride = new Ride();

        $activity = $this->createMockActivity();
        $streamList = $this->createMockStreamList();

        $importer->setMockActivity($activity);
        $importer->setMockStreamList($streamList);

        $gpxFile = new GpxFile();
        $this->gpxService->method('createGpxFromStravaStream')
            ->willReturn($gpxFile);

        $this->gpxService->method('toXmlString')
            ->willReturn('<?xml version="1.0"?><gpx></gpx>');

        $this->uploadFaker->expects($this->once())
            ->method('fakeUpload')
            ->with(
                $this->isInstanceOf(Track::class),
                'trackFile',
                $this->isType('string'),
                'upload.gpx'
            );

        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Track::class));

        $this->entityManager->expects($this->once())
            ->method('flush');

        $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $track = $importer->importTrack();

        $this->assertInstanceOf(Track::class, $track);
        $this->assertEquals(12345, $track->getStravaActivityId());
        $this->assertEquals(Track::TRACK_SOURCE_STRAVA, $track->getSource());
        $this->assertEquals('Test Device', $track->getApp());
        $this->assertSame($user, $track->getUser());
        $this->assertSame($ride, $track->getRide());
        $this->assertEquals('testuser', $track->getUsername());
    }

    public function testImportTrackCallsGpxServiceWithCorrectParameters(): void
    {
        $importer = $this->createTestableTrackImporter();

        $user = new User();
        $user->setUsername('testuser');

        $ride = new Ride();

        $activity = $this->createMockActivity();
        $streamList = $this->createMockStreamList();

        $importer->setMockActivity($activity);
        $importer->setMockStreamList($streamList);

        $expectedLatLng = [
            [52.520008, 13.404954],
            [52.521000, 13.405000],
        ];
        $expectedAltitude = [100.0, 100.5];
        $expectedTime = [0, 5];

        $this->gpxService->expects($this->once())
            ->method('createGpxFromStravaStream')
            ->with(
                $expectedLatLng,
                $expectedAltitude,
                $expectedTime,
                $this->isInstanceOf(\DateTime::class)
            )
            ->willReturn(new GpxFile());

        $this->gpxService->method('toXmlString')
            ->willReturn('<?xml version="1.0"?><gpx></gpx>');

        $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $importer->importTrack();
    }

    public function testImportTrackHandlesActivityWithoutDeviceName(): void
    {
        $importer = $this->createTestableTrackImporter();

        $user = new User();
        $user->setUsername('testuser');

        $ride = new Ride();

        $activity = new \stdClass();
        $activity->start_date = '2024-06-24T17:25:00Z';
        $activity->timezone = '(GMT+01:00) Europe/Berlin';
        // No device_name property

        $streamList = $this->createMockStreamList();

        $importer->setMockActivity($activity);
        $importer->setMockStreamList($streamList);

        $this->gpxService->method('createGpxFromStravaStream')
            ->willReturn(new GpxFile());

        $this->gpxService->method('toXmlString')
            ->willReturn('<?xml version="1.0"?><gpx></gpx>');

        $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $track = $importer->importTrack();

        $this->assertNull($track->getApp());
    }

    public function testImportTrackParsesTimezoneCorrectly(): void
    {
        $importer = $this->createTestableTrackImporter();

        $user = new User();
        $user->setUsername('testuser');

        $ride = new Ride();

        $activity = new \stdClass();
        $activity->start_date = '2024-06-24T17:25:00Z';
        $activity->timezone = '(GMT+01:00) Europe/Berlin';
        $activity->device_name = 'Test Device';

        $streamList = $this->createMockStreamList();

        $importer->setMockActivity($activity);
        $importer->setMockStreamList($streamList);

        $capturedDateTime = null;
        $this->gpxService->expects($this->once())
            ->method('createGpxFromStravaStream')
            ->willReturnCallback(function ($latLng, $altitude, $time, \DateTime $dateTime) use (&$capturedDateTime) {
                $capturedDateTime = $dateTime;
                return new GpxFile();
            });

        $this->gpxService->method('toXmlString')
            ->willReturn('<?xml version="1.0"?><gpx></gpx>');

        $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $importer->importTrack();

        $this->assertNotNull($capturedDateTime);
        $this->assertEquals('Europe/Berlin', $capturedDateTime->getTimezone()->getName());
    }

    public function testImportTrackWithDifferentTimezones(): void
    {
        $importer = $this->createTestableTrackImporter();

        $user = new User();
        $user->setUsername('testuser');

        $ride = new Ride();

        $activity = new \stdClass();
        $activity->start_date = '2024-06-24T08:00:00Z';
        $activity->timezone = '(GMT-08:00) America/Los_Angeles';
        $activity->device_name = 'Test Device';

        $streamList = $this->createMockStreamList();

        $importer->setMockActivity($activity);
        $importer->setMockStreamList($streamList);

        $capturedDateTime = null;
        $this->gpxService->expects($this->once())
            ->method('createGpxFromStravaStream')
            ->willReturnCallback(function ($latLng, $altitude, $time, \DateTime $dateTime) use (&$capturedDateTime) {
                $capturedDateTime = $dateTime;
                return new GpxFile();
            });

        $this->gpxService->method('toXmlString')
            ->willReturn('<?xml version="1.0"?><gpx></gpx>');

        $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $importer->importTrack();

        $this->assertNotNull($capturedDateTime);
        $this->assertEquals('America/Los_Angeles', $capturedDateTime->getTimezone()->getName());
    }

    public function testImportTrackWithLargeStreamData(): void
    {
        $importer = $this->createTestableTrackImporter();

        $user = new User();
        $user->setUsername('testuser');

        $ride = new Ride();

        $activity = $this->createMockActivity();

        // Create a larger stream with 1000 points
        $streamList = new StreamList();

        $latLngData = [];
        $altitudeData = [];
        $timeData = [];

        for ($i = 0; $i < 1000; $i++) {
            $latLngData[] = [52.520008 + ($i * 0.0001), 13.404954 + ($i * 0.0001)];
            $altitudeData[] = 100.0 + ($i * 0.1);
            $timeData[] = $i * 5;
        }

        $latLngStream = new Stream();
        $latLngStream
            ->setType('latlng')
            ->setSeriesType('distance')
            ->setOriginalSize(1000)
            ->setResolution('high')
            ->setData($latLngData);

        $altitudeStream = new Stream();
        $altitudeStream
            ->setType('altitude')
            ->setSeriesType('distance')
            ->setOriginalSize(1000)
            ->setResolution('high')
            ->setData($altitudeData);

        $timeStream = new Stream();
        $timeStream
            ->setType('time')
            ->setSeriesType('distance')
            ->setOriginalSize(1000)
            ->setResolution('high')
            ->setData($timeData);

        $streamList
            ->addStream('latlng', $latLngStream)
            ->addStream('altitude', $altitudeStream)
            ->addStream('time', $timeStream);

        $importer->setMockActivity($activity);
        $importer->setMockStreamList($streamList);

        $this->gpxService->expects($this->once())
            ->method('createGpxFromStravaStream')
            ->with(
                $this->callback(function ($latLng) {
                    return count($latLng) === 1000;
                }),
                $this->callback(function ($altitude) {
                    return count($altitude) === 1000;
                }),
                $this->callback(function ($time) {
                    return count($time) === 1000;
                }),
                $this->isInstanceOf(\DateTime::class)
            )
            ->willReturn(new GpxFile());

        $this->gpxService->method('toXmlString')
            ->willReturn('<?xml version="1.0"?><gpx></gpx>');

        $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $track = $importer->importTrack();

        $this->assertInstanceOf(Track::class, $track);
    }

    private function createTestableTrackImporter(): TestableTrackImporter
    {
        return new TestableTrackImporter(
            $this->gpxService,
            $this->requestStack,
            $this->registry,
            $this->uploadFaker,
            '12345',
            'test-secret'
        );
    }

    private function createMockActivity(): \stdClass
    {
        $activity = new \stdClass();
        $activity->start_date = '2024-06-24T17:25:00Z';
        $activity->timezone = '(GMT+01:00) Europe/Berlin';
        $activity->device_name = 'Test Device';

        return $activity;
    }

    private function createMockStreamList(): StreamList
    {
        $streamList = new StreamList();

        // LatLng Stream
        $latLngStream = new Stream();
        $latLngStream
            ->setType('latlng')
            ->setSeriesType('distance')
            ->setOriginalSize(2)
            ->setResolution('high')
            ->setData([
                [52.520008, 13.404954],
                [52.521000, 13.405000],
            ]);

        // Altitude Stream
        $altitudeStream = new Stream();
        $altitudeStream
            ->setType('altitude')
            ->setSeriesType('distance')
            ->setOriginalSize(2)
            ->setResolution('high')
            ->setData([100.0, 100.5]);

        // Time Stream
        $timeStream = new Stream();
        $timeStream
            ->setType('time')
            ->setSeriesType('distance')
            ->setOriginalSize(2)
            ->setResolution('high')
            ->setData([0, 5]);

        $streamList
            ->addStream('latlng', $latLngStream)
            ->addStream('altitude', $altitudeStream)
            ->addStream('time', $timeStream);

        return $streamList;
    }
}
