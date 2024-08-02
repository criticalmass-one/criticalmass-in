<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Importer;

use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\User;
use Iamstuartwilson\StravaApi;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractTrackImporter implements TrackImporterInterface
{
    protected int $activityId;

    protected User $user;

    protected Ride $ride;

    protected StravaApi $api;

    const API_URI = 'https://www.strava.com/api/v3/';
    const RESOULUTION = 'high';

    protected $types = [
        'time',
        'latlng',
        'altitude',
    ];

    public function __construct(
        protected readonly GpxWriter $gpxWriter,
        protected readonly SessionInterface $session,
        protected readonly ManagerRegistry $registry,
        protected readonly UploadFakerInterface $uploadFaker,
        protected readonly SerializerInterface $serializer,
        string $stravaClientId,
        string $stravaSecret
    )
    {
        $this->api = $this->createApi((int)$stravaClientId, $stravaSecret);
    }

    protected function createApi(int $stravaClientId, string $stravaSecret): StravaApi
    {
        $api = new StravaApi($stravaClientId, $stravaSecret);

        /** @var StravaTokenStorage $token */
        $token = $this->session->get('strava_token');
        $api = StravaTokenStorage::setAccessToken($api, $token);

        return $api;
    }

    public function setUser(User $user): TrackImporterInterface
    {
        $this->user = $user;

        return $this;
    }

    public function setRide(Ride $ride): TrackImporterInterface
    {
        $this->ride = $ride;

        return $this;
    }

    public function setStravaActivityId(int $activityId): TrackImporterInterface
    {
        $this->activityId = $activityId;

        return $this;
    }
}
