<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Importer;

use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Strava\Stream\StreamFactory;
use App\Criticalmass\Strava\Stream\StreamList;
use App\Criticalmass\Strava\Stream\StreamListConverter;
use App\Criticalmass\Strava\Token\StravaTokenStorage;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Iamstuartwilson\StravaApi;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TrackImporter implements TrackImporterInterface
{
    private int $activityId;
    private User $user;
    private Ride $ride;
    private StravaApi $api;
    private const string API_URI = 'https://www.strava.com/api/v3/';
    private const string RESOULUTION = 'high';

    protected $types = [
        'time',
        'latlng',
        'altitude',
    ];

    public function __construct(
        private readonly GpxWriter $gpxWriter,
        private readonly SessionInterface $session,
        private readonly ManagerRegistry $registry,
        private readonly UploadFakerInterface $uploadFaker,
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

    protected function getActivity(bool $allEfforts = true): \stdClass
    {
        return $this->api->get(sprintf('activities/%d', $this->activityId));
    }

    protected function getActivityStreamList(): StreamList
    {
        $response = $this->api->get(sprintf('/activities/%d/streams', $this->activityId), [
            'keys' => implode(',', $this->types),
            'key_by_type' => true,
        ]);

        return StreamFactory::build($response);
    }

    protected function getStartDateTime(): \DateTime
    {
        $activity = $this->getActivity();

        [$offset, $timezoneIdentifier] = explode(' ', (string) $activity->timezone);
        
        $startDateTime = new \DateTime($activity->start_date);
        $startDateTime->setTimezone(new \DateTimeZone($timezoneIdentifier));

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
}
