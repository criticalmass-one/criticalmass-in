<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\FitService;

use adriangibbons\phpFITFileAnalysis;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use phpGPX\Models\Point;
use phpGPX\Models\Segment;
use phpGPX\Models\Track as GpxTrack;

/**
 * Converts Garmin FIT files into the same phpGPX model the rest of the application
 * already uses for GPX tracks.
 *
 * FIT uploads are normalised to GPX on ingest, so the entire downstream pipeline
 * (TrackEventSubscriber, GpxService, TrackValidator) stays untouched — mirroring the
 * approach the former Strava importer used (build a GpxFile, persist it as GPX,
 * dispatch TrackUploadedEvent).
 */
class FitToGpxConverter implements FitToGpxConverterInterface
{
    public function convertFileToGpxFile(string $fitFilePath): GpxFile
    {
        try {
            // Default options: positions are converted from semicircles to degrees,
            // and timestamps are converted to Unix time.
            $fit = new phpFITFileAnalysis($fitFilePath);
        } catch (\Throwable $e) {
            throw new \RuntimeException(sprintf('Unable to parse FIT file: %s', $e->getMessage()), 0, $e);
        }

        $records = $fit->data_mesgs['record'] ?? [];

        // Each record field is keyed by the record's Unix timestamp. Iterating the
        // latitude array therefore yields the points in chronological (insertion) order.
        $latitudes = $records['position_lat'] ?? [];
        $longitudes = $records['position_long'] ?? [];
        $altitudes = $records['enhanced_altitude'] ?? ($records['altitude'] ?? []);

        $points = [];

        foreach ($latitudes as $timestamp => $latitude) {
            // Records without a GPS fix (indoor activities, pauses) carry no longitude — skip them.
            if (!isset($longitudes[$timestamp])) {
                continue;
            }

            $point = new Point(Point::TRACKPOINT);
            $point->latitude = (float) $latitude;
            $point->longitude = (float) $longitudes[$timestamp];
            $point->elevation = isset($altitudes[$timestamp]) ? (float) $altitudes[$timestamp] : null;
            // FIT-Zeitstempel sind Unix/UTC. `@<timestamp>` erzeugt eine DateTime
            // in UTC (unabhängig von der Server-Zeitzone), damit die minutengenaue
            // Ride-Auto-Zuordnung (DateTimeVoter) korrekt matcht.
            $point->time = new \DateTime('@' . (int) $timestamp);

            $points[] = $point;
        }

        if (count($points) === 0) {
            throw new \RuntimeException('FIT file contains no GPS track points (position_lat/position_long).');
        }

        $segment = new Segment();
        $segment->points = $points;

        $gpxTrack = new GpxTrack();
        $gpxTrack->segments[] = $segment;

        $gpxFile = new GpxFile();
        $gpxFile->metadata = new Metadata();
        $gpxFile->metadata->time = clone $points[0]->time;
        $gpxFile->tracks[] = $gpxTrack;

        return $gpxFile;
    }

    public function convertFileToXmlString(string $fitFilePath): string
    {
        return $this->convertFileToGpxFile($fitFilePath)->toXML()->saveXML();
    }
}
