<?php declare(strict_types=1);

namespace Tests\DistanceCalculator;

use App\Criticalmass\Geo\GpxReader\GpxReader;
use App\Criticalmass\Geo\TimeShifter\GpxTimeShifter;
use PHPUnit\Framework\TestCase;

class GpxTimeShifterTest extends TestCase
{
    public function testGpxTimeShifter1()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();

        $dateTime = $gpxReader
            ->loadFromFile($gpxTestFilename)
            ->getDateTimeOfPoint(5)
        ;

        $this->assertEquals(new \DateTime('2016-11-25 15:40:29'), $dateTime);
    }

    public function testGpxTimeShifter2()
    {
        $gpxTestFilename = __DIR__.'/../Files/bahnhof.gpx';

        $gpxReader = new GpxReader();
        $timeShifter = new GpxTimeShifter($gpxReader);

        $interval = new \DateInterval('PT5M');

        $timeShifter
            ->loadGpxFile($gpxTestFilename)
            ->shift($interval)
        ;

        $positionList = $timeShifter->getPositionList();

        $dateTime = $positionList
            ->get(5)
            ->getDateTime()
        ;

        $this->assertEquals(new \DateTime('2016-11-25 15:40:29'), $dateTime);
    }
}
