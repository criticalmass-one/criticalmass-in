<?php declare(strict_types=1);

namespace Tests\Strava\Importer;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Criticalmass\Strava\Importer\TrackImporter;
use App\Criticalmass\Strava\Importer\TrackImporterInterface;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TrackImporterTest extends TestCase
{
    private GpxServiceInterface $gpxService;
    private RequestStack $requestStack;
    private ManagerRegistry $registry;
    private UploadFakerInterface $uploadFaker;
    private SessionInterface $session;

    protected function setUp(): void
    {
        $this->gpxService = $this->createMock(GpxServiceInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->uploadFaker = $this->createMock(UploadFakerInterface::class);
        $this->session = $this->createMock(SessionInterface::class);

        // Setup session with token
        $token = new StravaTokenStorage('test-access-token', 'test-refresh-token', time() + 3600);
        $this->session->method('get')
            ->with('strava_token')
            ->willReturn($token);

        $this->requestStack->method('getSession')
            ->willReturn($this->session);
    }

    public function testImplementsInterface(): void
    {
        $importer = $this->createTrackImporter();

        $this->assertInstanceOf(TrackImporterInterface::class, $importer);
    }

    public function testSetUserReturnsInterface(): void
    {
        $importer = $this->createTrackImporter();
        $user = new User();

        $result = $importer->setUser($user);

        $this->assertInstanceOf(TrackImporterInterface::class, $result);
    }

    public function testSetRideReturnsInterface(): void
    {
        $importer = $this->createTrackImporter();
        $ride = new Ride();

        $result = $importer->setRide($ride);

        $this->assertInstanceOf(TrackImporterInterface::class, $result);
    }

    public function testSetStravaActivityIdReturnsInterface(): void
    {
        $importer = $this->createTrackImporter();

        $result = $importer->setStravaActivityId(12345);

        $this->assertInstanceOf(TrackImporterInterface::class, $result);
    }

    public function testFluentInterfaceChaining(): void
    {
        $importer = $this->createTrackImporter();
        $user = new User();
        $ride = new Ride();

        $result = $importer
            ->setUser($user)
            ->setRide($ride)
            ->setStravaActivityId(12345);

        $this->assertInstanceOf(TrackImporterInterface::class, $result);
    }

    private function createTrackImporter(): TrackImporter
    {
        return new TrackImporter(
            $this->gpxService,
            $this->requestStack,
            $this->registry,
            $this->uploadFaker,
            '12345',
            'test-secret'
        );
    }
}
