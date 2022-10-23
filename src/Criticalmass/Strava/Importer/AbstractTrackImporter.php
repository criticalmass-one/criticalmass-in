<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Importer;

use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\User;
use Iamstuartwilson\StravaApi;
use JMS\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractTrackImporter implements TrackImporterInterface
{
    /** @var int $activityId */
    protected $activityId;

    /** @var User $user */
    protected $user;

    /** @var Ride $ride */
    protected $ride;

    /** @var GpxWriter $gpxWriter */
    protected $gpxWriter;

    /** @var SessionInterface $session */
    protected $session;

    /** @var StravaApi $api */
    protected $api;

    /** @var UploadFakerInterface $uploadFaker */
    protected $uploadFaker;

    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    const API_URI = 'https://www.strava.com/api/v3/';
    const RESOULUTION = 'high';

    protected $types = [
        'time',
        'latlng',
        'altitude',
    ];

    public function __construct(GpxWriter $gpxWriter, SessionInterface $session, ManagerRegistry $registry, UploadFakerInterface $uploadFaker, SerializerInterface $serializer, string $stravaClientId, string $stravaSecret)
    {
        $this->gpxWriter = $gpxWriter;
        $this->session = $session;
        $this->uploadFaker = $uploadFaker;
        $this->registry = $registry;
        $this->serializer = $serializer;

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
