<?php

namespace Caldera\CriticalmassCoreBundle\Tests\Utility\GpxReader\GpxCoordLoop;


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
        <time>2015-04-03T06:46:56.174Z</time>
    </metadata>
    <trk>
        <name>Testtrack</name>
        <trkseg>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T01:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T02:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T03:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T04:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T05:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T06:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T07:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T08:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T09:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T10:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T11:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T12:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T13:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T14:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T15:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T16:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T17:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T18:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T19:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T20:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T21:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T22:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T23:14:49Z</time>
            </trkpt>
            <trkpt lon="9.907195" lat="53.606435">
                <time>2015-04-01T24:14:49Z</time>
            </trkpt>
        </trkseg>
    </trk>
</gpx>
EOF;

    }
    
    public function testGpxLoop1()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);

        $gcl = new GpxCoordLoop($gr);

        $searchDateTime = new \DateTime('2015-04-01T08:14:49Z');
        $result = $gcl->execute($searchDateTime);

        $this->assertEquals(7, $result);
    }
    
    public function testGpxLoop2()
    {
        $gr = new GpxReader();
        $gr->loadString($this->gpx);
        
        $gcl = new GpxCoordLoop($gr);
        
        $searchDateTime = new \DateTime('2015-04-01T15:14:49Z');
        $result = $gcl->execute($searchDateTime);
        
        $this->assertEquals(14, $result);
    }
}