<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Importer;

use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use Strava\API\Service\REST;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Strava\API\Client;

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

    /** @var Client $client */
    protected $client;

    /** @var UploadFakerInterface $uploadFaker */
    protected $uploadFaker;

    /** @var RegistryInterface $registry */
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

    public function __construct(GpxWriter $gpxWriter, SessionInterface $session, RegistryInterface $registry, UploadFakerInterface $uploadFaker, SerializerInterface $serializer)
    {
        $this->gpxWriter = $gpxWriter;
        $this->session = $session;
        $this->uploadFaker = $uploadFaker;
        $this->registry = $registry;
        $this->serializer = $serializer;

        $this->client = $this->createClient();
    }

    protected function createClient(): Client
    {
        $token = $this->session->get('strava_token');

        $adapter = new \GuzzleHttp\Client(['base_uri' => self::API_URI]);
        $service = new REST($token, $adapter);

        return new Client($service);
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
