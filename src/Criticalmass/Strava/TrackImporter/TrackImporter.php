<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Strava\API\Client;
use Strava\API\Service\REST;

class TrackImporter implements TrackImporterInterface
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

    protected function getActivity(bool $allEfforts = true): array
    {
        return $this->client->getActivity($this->activityId, $allEfforts);
    }

    protected function getActivityStreamList(): StreamList
    {
        $response = $this->client->getStreamsActivity($this->activityId, implode(',', $this->types), self::RESOULUTION);

        return StreamFactory::build($response);
    }

    protected function getStartDateTime(): \DateTime
    {
        $activity = $this->getActivity();

        $startDateTime = new \DateTime($activity['start_date']);
        $startDateTime->setTimezone(new \DateTimeZone($activity['timezone']));

        return $startDateTime;
    }

    protected function getStartDateTimestamp(): int
    {
        return $this->getStartDateTime()->getTimestamp();
    }

    protected function createPositionList(): PositionList
    {
        $startDateTime = $this->getStartDateTime();

        $streamList = $this->getActivityStreamList();

        return StreamListConverter::convert($streamList, $startDateTime);
    }

    protected function createTrack(): Track
    {
        $track = new Track();
        $track
            ->setStravaActivityId($this->activityId)
            ->setSource(Track::TRACK_SOURCE_STRAVA)
            ->setUser($this->user)
            ->setRide($this->ride)
            ->setUsername($this->user->getUsername());

        return $track;
    }

    public function importTrack(): Track
    {
        $positionList = $this->createPositionList();

        $this->gpxWriter->setPositionList($positionList)->generateGpxContent();

        $fileContent = $this->gpxWriter->getGpxContent();

        $track = $this->createTrack();

        $this->uploadFaker->fakeUpload($track, 'trackFile', $fileContent, 'upload.gpx');

        $em = $this->registry->getManager();
        $em->persist($track);
        $em->flush();

        return $track;
    }

    public function setUser(User $user): TrackImporterInterface
    {
        $this->setUser($user);

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
