<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\GpxWriter\GpxWriter;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Entity\Track;
use Pest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Strava\API\Client;
use Strava\API\Service\REST;

class TrackImporter
{
    /** @var GpxWriter $gpxWriter */
    protected $gpxWriter;

    /** @var string $uploadDestinationTrack */
    protected $uploadDestinationTrack;

    /** @var SessionInterface $session */
    protected $session;

    /** @var Client $client */
    protected $client;

    const API_URI = 'https://www.strava.com/api/v3';
    const RESOULUTION = 'high';

    public function __construct(GpxWriter $gpxWriter, SessionInterface $session, string $uploadDestinationTrack)
    {
        $this->gpxWriter = $gpxWriter;
        $this->session = $session;
        $this->uploadDestinationTrack = $uploadDestinationTrack;

        $this->client = $this->createClient();
    }

    protected function createClient(): Client
    {
        $token = $this->session->get('strava_token');

        $adapter = new Pest(self::API_URI);
        $service = new REST($token, $adapter);

        return new Client($service);
    }

    protected function getActivity(int $activityId, bool $allEfforts = true): array
    {
        /* Catch the activity to retrieve the start dateTime */
        $activity = $this->client->getActivity($activityId, $allEfforts);

        return $activity;
    }

    protected function getStartDateTime(array $activity): \DateTime
    {
        $startDateTime = new \DateTime($activity['start_date']);
        $startDateTime->setTimezone(new \DateTimeZone($activity['timezone']));

        return $startDateTime;
    }

    protected function createPositionList(int $activityId): PositionList
    {
        /* Now fetch all the gpx data we need */
        $activityStream = $this->client->getStreamsActivity($activityId, 'time,latlng,altitude', self::RESOULUTION);
        $activity = $this->getActivity($activityId);

        $startTimestamp = $this->getStartDateTime($activity)->getTimestamp();

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

    protected function createTrack(int $activityId, string $filename): Track
    {
        $track = new Track();
        $track
            ->setStravaActivityId($activityId)
            ->setTrackFilename($filename);

        return $track;
    }

    public function doMagic(int $activityId): Track
    {
        $positionList = $this->createPositionList($activityId);

        $this->gpxWriter->setPositionList($positionList);

        $this->gpxWriter->saveGpxContent();

        $filename = sprintf('%s.gpx', uniqid());

        $fp = fopen(sprintf('%s/%s', $this->uploadDestinationTrack, $filename), 'w');
        fwrite($fp, $exporter->getGpxContent());
        fclose($fp);



        $em = $this->getDoctrine()->getManager();
        $em->persist($track);
        $em->flush();
    }
}
