<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Stream;

use App\Criticalmass\Geo\Entity\Position;
use App\Criticalmass\Geo\PositionList\PositionList;

class StreamListConverter
{
    private function __construct()
    {
    }

    public static function convert(StreamList $streamList, \DateTime $startDateTime): PositionList
    {
        $positionList = new PositionList();
        $length = $streamList->getLength();
        $startTimestamp = (int) $startDateTime->format('U');

        for ($i = 0; $i < $length; ++$i) {
            $latitude = $streamList->getStream('latlng')->getData()[$i][0];
            $longitude = $streamList->getStream('latlng')->getData()[$i][1];

            $position = new Position($latitude, $longitude);

            if ($i > 0) {
                $altitude = round($streamList->getStream('altitude')->getData()[$i] - $streamList->getStream('altitude')->getData()[$i - 1], 2);
            } else {
                $altitude = round($streamList->getStream('altitude')->getData()[$i], 2);
            }

            $timestamp = $startTimestamp + $streamList->getStream('time')->getData()[$i];

            $position
                ->setAltitude($altitude)
                ->setTimestamp($timestamp)
                ->setDateTime(new \DateTime(sprintf('@%d', $timestamp)));

            $positionList->add($position);
        }

        return $positionList;
    }
}