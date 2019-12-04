<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Converter;

use App\Entity\TrackImportCandidate;
use Caldera\GeoBasic\Coord\Coord;

class StravaActivityConverter
{
    private function __construct()
    {

    }

    public static function convert(array $content): TrackImportCandidate
    {
        $startCoord = new Coord($content['start_latlng'][0], $content['start_latlng'][1]);
        $endCoord = new Coord($content['end_latlng'][0], $content['end_latlng'][1]);

        $model = new TrackImportCandidate();
        $model
            ->setActivityId($content['id'])
            ->setName($content['name'])
            ->setDistance($content['distance'])
            ->setElapsedTime($content['elapsed_time'])
            ->setStartDateTime(new \DateTime($content['start_date']))
            ->setStartCoord($startCoord)
            ->setEndCoord($endCoord)
            ->setType($content['type'])
            ->setPolyline($content['map']['summary_polyline']);

        return $model;
    }
}