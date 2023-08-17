<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Importer;

use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Strava\Stream\StreamFactory;
use App\Criticalmass\Strava\Stream\StreamList;
use App\Criticalmass\Strava\Stream\StreamListConverter;
use App\Entity\Track;

class TrackImporter extends AbstractTrackImporter
{
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
