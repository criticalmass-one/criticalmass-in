<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxService;

use App\Entity\Track;
use App\Enum\PolylineResolution;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track as GpxTrack;
use phpGPX\phpGPX;
use PointReduction\Algorithms\RadialDistance;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GpxService implements GpxServiceInterface
{
    private const LAT_LNG_LIST_SAMPLE_WIDTH = 10;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag,
    ) {
    }

    public function loadFromFile(string $path): GpxFile
    {
        $phpGpx = new phpGPX();

        return $phpGpx->load($path);
    }

    public function loadFromTrack(Track $track): GpxFile
    {
        $trackDirectory = $this->parameterBag->get('upload_destination.track');
        $filename = sprintf('%s/%s', $trackDirectory, $track->getTrackFilename());

        return $this->loadFromFile($filename);
    }

    /**
     * @return Point[]
     */
    public function getPoints(Track $track): array
    {
        $gpxFile = $this->loadFromTrack($track);

        return $gpxFile->tracks[0]->getPoints();
    }

    /**
     * Returns points within the track's startPoint and endPoint range.
     *
     * @return Point[]
     */
    public function getPointsInRange(Track $track): array
    {
        $allPoints = $this->getPoints($track);

        $startPoint = $track->getStartPoint();
        $endPoint = $track->getEndPoint();

        return array_slice($allPoints, $startPoint, $endPoint - $startPoint + 1);
    }

    /**
     * Binary search to find the point closest to the given datetime.
     */
    public function findPointAtTime(Track $track, \DateTimeInterface $dateTime, ?\DateTimeZone $timeZone = null): ?Point
    {
        $points = $this->getPointsInRange($track);

        if (empty($points)) {
            return null;
        }

        $targetTimestamp = $dateTime->getTimestamp();

        $left = 0;
        $right = count($points) - 1;

        while ($left <= $right) {
            $mid = (int) floor(($left + $right) / 2);
            $point = $points[$mid];

            if (!$point->time) {
                return null;
            }

            $pointTimestamp = $point->time->getTimestamp();

            if ($pointTimestamp === $targetTimestamp) {
                return $point;
            }

            if ($pointTimestamp < $targetTimestamp) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        // Return the closest point
        if ($left >= count($points)) {
            return $points[count($points) - 1];
        }

        if ($right < 0) {
            return $points[0];
        }

        $leftPoint = $points[$left];
        $rightPoint = $points[$right];

        $leftDiff = abs($leftPoint->time->getTimestamp() - $targetTimestamp);
        $rightDiff = abs($rightPoint->time->getTimestamp() - $targetTimestamp);

        return $leftDiff < $rightDiff ? $leftPoint : $rightPoint;
    }

    public function generatePolylineAtResolution(Track $track, PolylineResolution $resolution): string
    {
        $points = $this->getPointsInRange($track);
        $pointList = $this->pointsToArray($points);

        $reducer = new RadialDistance($pointList);
        $reducedPointList = $reducer->reduce($resolution->tolerance());

        $reducedList = [];
        foreach ($reducedPointList as $point) {
            $reducedList[] = [$point->x, $point->y];
        }

        return \Polyline::Encode($reducedList);
    }

    /**
     * Calculates the total distance of the track in kilometers.
     */
    public function calculateDistance(Track $track): float
    {
        $points = $this->getPointsInRange($track);

        if (count($points) <= 1) {
            return 0.0;
        }

        $distance = 0.0;
        $prevPoint = $points[0];

        for ($i = 1; $i < count($points); $i++) {
            $currentPoint = $points[$i];
            $distance += $this->calculateDistanceBetweenPoints(
                $prevPoint->latitude,
                $prevPoint->longitude,
                $currentPoint->latitude,
                $currentPoint->longitude
            );
            $prevPoint = $currentPoint;
        }

        return $distance;
    }

    /**
     * Generates a JSON-encoded lat/lng list for frontend consumption.
     * Samples every N points for reduced payload size.
     */
    public function generateLatLngList(Track $track, int $sampleWidth = self::LAT_LNG_LIST_SAMPLE_WIDTH): string
    {
        $points = $this->getPointsInRange($track);

        $result = [];
        foreach ($points as $index => $point) {
            if ($index % $sampleWidth === 0) {
                $result[] = sprintf('[%s,%s]', $point->latitude, $point->longitude);
            }
        }

        return '[' . implode(',', $result) . ']';
    }

    /**
     * Returns the start datetime from track stats.
     */
    public function getStartDateTime(Track $track): ?\DateTime
    {
        $gpxFile = $this->loadFromTrack($track);
        $points = $this->getPointsInRange($track);

        if (empty($points)) {
            return null;
        }

        $firstPoint = reset($points);

        return $firstPoint->time ? \DateTime::createFromInterface($firstPoint->time) : null;
    }

    /**
     * Returns the end datetime from track stats.
     */
    public function getEndDateTime(Track $track): ?\DateTime
    {
        $points = $this->getPointsInRange($track);

        if (empty($points)) {
            return null;
        }

        $lastPoint = end($points);

        return $lastPoint->time ? \DateTime::createFromInterface($lastPoint->time) : null;
    }

    /**
     * Converts phpGPX Points to an array format suitable for polyline encoding.
     *
     * @param Point[] $points
     * @return array<array{float, float}>
     */
    private function pointsToArray(array $points): array
    {
        $pointList = [];

        foreach ($points as $point) {
            $pointList[] = [$point->latitude, $point->longitude];
        }

        return $pointList;
    }

    /**
     * Generates a JSON-encoded lat/lng list including timestamps for timelapse.
     * Format: [["2024-01-01T12:00:00Z", lat, lng], ...]
     */
    public function generateTimeLatLngList(Track $track, int $sampleWidth = self::LAT_LNG_LIST_SAMPLE_WIDTH): string
    {
        $points = $this->getPointsInRange($track);

        $result = [];
        foreach ($points as $index => $point) {
            if ($index % $sampleWidth === 0 && $point->time) {
                $result[] = sprintf(
                    '["%s",%s,%s]',
                    $point->time->format('Y-m-d\TH:i:s\Z'),
                    $point->latitude,
                    $point->longitude
                );
            }
        }

        return '[' . implode(',', $result) . ']';
    }

    /**
     * Generates a simple lat/lng list from all points (not just range).
     * Used for track range editing.
     */
    public function generateSimpleLatLngList(Track $track, int $sampleWidth = self::LAT_LNG_LIST_SAMPLE_WIDTH): string
    {
        $allPoints = $this->getPoints($track);

        $result = [];
        foreach ($allPoints as $index => $point) {
            if ($index % $sampleWidth === 0) {
                $result[] = sprintf('[%s,%s]', $point->latitude, $point->longitude);
            }
        }

        return '[' . implode(',', $result) . ']';
    }

    /**
     * Shifts all timestamps in a track by the given interval and saves the GPX file.
     */
    public function shiftTimeAndSave(Track $track, \DateInterval $interval): void
    {
        $gpxFile = $this->loadFromTrack($track);
        $gpxTrack = $gpxFile->tracks[0];

        foreach ($gpxTrack->segments as $segment) {
            foreach ($segment->points as $point) {
                if ($point->time) {
                    $newTime = clone $point->time;
                    $newTime->add($interval);
                    $point->time = $newTime;
                }
            }
        }

        $trackDirectory = $this->parameterBag->get('upload_destination.track');
        $filename = sprintf('%s/%s', $trackDirectory, $track->getTrackFilename());

        $gpxFile->save($filename, phpGPX::XML_FORMAT);
    }

    /**
     * Creates a GpxFile from Strava stream data.
     *
     * @param array<array{float, float}> $latLngData Array of [lat, lng] pairs
     * @param array<float> $altitudeData Array of altitude values
     * @param array<int> $timeData Array of time offsets in seconds
     * @param \DateTime $startDateTime Start datetime of the activity
     */
    public function createGpxFromStravaStream(
        array $latLngData,
        array $altitudeData,
        array $timeData,
        \DateTime $startDateTime
    ): GpxFile {
        $gpxFile = new GpxFile();
        $gpxFile->metadata = new Metadata();
        $gpxFile->metadata->time = $startDateTime;

        $gpxTrack = new GpxTrack();
        $segment = new Segment();

        $startTimestamp = $startDateTime->getTimestamp();
        $length = count($latLngData);

        for ($i = 0; $i < $length; $i++) {
            $point = new Point(Point::TRACKPOINT);
            $point->latitude = $latLngData[$i][0];
            $point->longitude = $latLngData[$i][1];
            $point->elevation = $altitudeData[$i] ?? null;

            $timestamp = $startTimestamp + ($timeData[$i] ?? 0);
            $point->time = new \DateTime(sprintf('@%d', $timestamp));

            $segment->points[] = $point;
        }

        $gpxTrack->segments[] = $segment;
        $gpxFile->tracks[] = $gpxTrack;

        return $gpxFile;
    }

    /**
     * Converts a GpxFile to XML string content.
     */
    public function toXmlString(GpxFile $gpxFile): string
    {
        return $gpxFile->toXML()->saveXML();
    }

    /**
     * Simplified distance calculation between two coordinates.
     * Returns distance in kilometers.
     */
    private function calculateDistanceBetweenPoints(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $dx = 71.5 * ($lon1 - $lon2);
        $dy = 111.3 * ($lat1 - $lat2);

        return sqrt($dx * $dx + $dy * $dy);
    }
}
