<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\UploadFaker\UploadFakerInterface;
use App\Entity\Track;
use JMS\Serializer\SerializerInterface;
use Pest;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Strava\API\Client;
use Strava\API\Service\REST;

class TrackImporter implements TrackImporterInterface
{
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

    protected function getActivity(int $activityId, bool $allEfforts = true): array
    {
        return $this->client->getActivity($activityId, $allEfforts);
    }

    protected function getActivityStreamList(int $activityId): StreamList
    {
        $response = $this->client->getStreamsActivity($activityId, implode(',', $this->types), self::RESOULUTION);

        return StreamFactory::build($response);
    }

    protected function getStartDateTime(array $activity): \DateTime
    {
        $startDateTime = new \DateTime($activity['start_date']);
        $startDateTime->setTimezone(new \DateTimeZone($activity['timezone']));

        return $startDateTime;
    }

    protected function getStartDateTimestamp(int $activityId): int
    {
        return $this->getStartDateTime($this->getActivity($activityId))->getTimestamp();
    }

    protected function createPositionList(int $activityId): PositionList
    {
        $startTimestamp = $this->getStartDateTimestamp($activityId);

        $streamList = $this->getActivityStreamList($activityId);
        
        $length = count($activityStream[0]['data']);

        $latLngList = $activityStream[0]['data'];
        $timeList = $activityStream[1]['data'];
        $altitudeList = $activityStream[2]['data'];

        $positionList = new PositionList();

        for ($i = 0; $i < $length; ++$i) {
            $latitude = $latLngList[$i][0];
            $longitude = $latLngList[$i][1];

            $position = new Position($latitude, $longitude);

            $altitude = round($i > 0 ? $altitudeList[$i] - $altitudeList[$i - 1] : $altitudeList[$i], 2);

            $position->setAltitude($altitude);
            $position->setTimestamp($startTimestamp + $timeList[$i]);

            $positionList->add($position);
        }

        return $positionList;
    }

    public function doMagic(int $activityId): Track
    {
        $positionList = $this->createPositionList($activityId);

        $this->gpxWriter->setPositionList($positionList);

        $fileContent = $this->gpxWriter->getGpxContent();

        $track = new Track();
        $track
            ->setStravaActivityId($activityId)
            ->setSource(Track::TRACK_SOURCE_STRAVA);

        $this->uploadFaker->fakeUpload($track, 'trackFile', $fileContent);

        $em = $this->registry->getManager();
        $em->persist($track);
        $em->flush();
    }
}
