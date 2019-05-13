<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;

class StreamListConverter
{
    private function __construct()
    {
    }

    public static function convert(StreamList $streamList, int $starTimestamp): PositionList
    {
        $positionList = new PositionList();
        $length = $streamList->getLength();

        for ($i = 0; $i < $length; ++$i) {
            $latitude = $streamList->getStream('latlng')->getData()[$i][0];
            $longitude = $streamList->getStream('latlng')->getData()[$i][1];

            $position = new Position($latitude, $longitude);

            if ($i > 0) {
                $altitude = round($streamList->getStream('altitude')->getData()[$i] - $streamList->getStream('altitude')->getData()[$i - 1], 2);
            } else {
                $altitude = round($streamList->getStream('altitude')->getData()[$i], 2);
            }

            $timestamp = $starTimestamp + $streamList->getStream('time')->getData()[$i];

            $position
                ->setAltitude($altitude)
                ->setTimestamp($timestamp);

            $positionList->add($position);
        }

        return $positionList;
    }
}