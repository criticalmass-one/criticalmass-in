<?php

namespace Caldera\CriticalmassCoreBundle\Tests\Utility\GpxReader;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxCoordLoop\GpxCoordLoop;
use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;

class GpxCoordLoopTest extends \PHPUnit_Framework_TestCase
{
    protected $gpx;
    
    protected function setUp()
    {
        $this->gpx = <<<EOF
<gpx xmlns="http://www.topografix.com/GPX/1/1" xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3" xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1.1" creator="Adze - http://kobotsw.com/apps/adze" xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd">
    <metadata>
        <name>Testtrack</name>
        <time>2015-04-01T06:46:56.174Z</time>
    </metadata>
    <trk>
        <name>Testtrack</name>
        <trkseg>
            <trkpt lon="1.907195" lat="1.606435">
                <time>2015-04-01T01:14:49Z</time>
            </trkpt>
            <trkpt lon="2.907195" lat="2.606435">
                <time>2015-04-01T02:14:49Z</time>
            </trkpt>
            <trkpt lon="3.907195" lat="3.606435">
                <time>2015-04-01T03:14:49Z</time>
            </trkpt>
            <trkpt lon="4.907195" lat="4.606435">
                <time>2015-04-01T04:14:49Z</time>
            </trkpt>
            <trkpt lon="5.907195" lat="5.606435">
                <time>2015-04-01T05:14:49Z</time>
            </trkpt>
            <trkpt lon="6.907195" lat="6.606435">
                <time>2015-04-01T06:14:49Z</time>
            </trkpt>
            <trkpt lon="7.907195" lat="7.606435">
                <time>2015-04-01T07:14:49Z</time>
            </trkpt>
            <trkpt lon="8.907195" lat="8.606435">
                <time>2015-04-01T08:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="9.606435">
                <time>2015-04-01T09:14:49Z</time>
            </trkpt>
            <trkpt lon="10.907195" lat="10.606435">
                <time>2015-04-01T10:14:49Z</time>
            </trkpt>
            <trkpt lon="11.907195" lat="11.606435">
                <time>2015-04-01T11:14:49Z</time>
            </trkpt>
            <trkpt lon="12.907195" lat="12.606435">
                <time>2015-04-01T12:14:49Z</time>
            </trkpt>
            <trkpt lon="13.907195" lat="13.606435">
                <time>2015-04-01T13:14:49Z</time>
            </trkpt>
            <trkpt lon="14.907195" lat="14.606435">
                <time>2015-04-01T14:14:49Z</time>
            </trkpt>
            <trkpt lon="15.907195" lat="15.606435">
                <time>2015-04-01T15:14:49Z</time>
            </trkpt>
            <trkpt lon="16.907195" lat="16.606435">
                <time>2015-04-01T16:14:49Z</time>
            </trkpt>
            <trkpt lon="17.907195" lat="17.606435">
                <time>2015-04-01T17:14:49Z</time>
            </trkpt>
            <trkpt lon="18.907195" lat="18.606435">
                <time>2015-04-01T18:14:49Z</time>
            </trkpt>
            <trkpt lon="19.907195" lat="19.606435">
                <time>2015-04-01T19:14:49Z</time>
            </trkpt>
            <trkpt lon="20.907195" lat="20.606435">
                <time>2015-04-01T20:14:49Z</time>
            </trkpt>
            <trkpt lon="21.907195" lat="21.606435">
                <time>2015-04-01T21:14:49Z</time>
            </trkpt>
            <trkpt lon="22.907195" lat="22.606435">
                <time>2015-04-01T22:14:49Z</time>
            </trkpt>
            <trkpt lon="23.907195" lat="23.606435">
                <time>2015-04-01T23:14:49Z</time>
            </trkpt>
            <trkpt lon="24.907195" lat="24.606435">
                <time>2015-04-01T24:14:49Z</time>
            </trkpt>
        </trkseg>
    </trk>
</gpx>
EOF;

    }

    public function testCreationDateTime()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(new \DateTime('2015-04-01T06:46:56.174Z'), $gr->getCreationDateTime());
    }

    public function testStartDateTime()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(new \DateTime('2015-04-01T01:14:49Z'), $gr->getStartDateTime());
    }

    public function testEndDateTime()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(new \DateTime('2015-04-01T24:14:49Z'), $gr->getEndDateTime());
    }

    public function testCountNodes()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(24, $gr->countPoints());
    }

    public function testMd5()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals('916a0d0fd0ecb12e110bd51d484f1968', $gr->getMd5Hash());
    }

    public function testLatitude()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(6.606435, $gr->getLatitudeOfPoint(5));
    }

    public function testLongitude()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(6.907195, $gr->getLongitudeOfPoint(5));
    }

    public function testTimestamp()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals('2015-04-01T06:14:49Z', $gr->getTimestampOfPoint(5));
    }

    public function testDateTime()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(new \DateTime('2015-04-01T06:14:49Z'), $gr->getDateTimeOfPoint(5));
    }

    public function testDistance()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $this->assertEquals(3042.61, $gr->calculateDistance());
    }

    public function testFindCoordNearDateTime()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $dateTime = new \DateTime('2015-04-01T20:14:49Z');
        $coords = $gr->findCoordNearDateTime($dateTime);

        $this->assertEquals(20.606435, $coords['latitude']);
        $this->assertEquals(20.907195, $coords['longitude']);
    }
}