<?php declare(strict_types=1);

namespace Tests\Geo\Converter\GpxPointToPositionConverter;

use App\Criticalmass\Geo\Converter\GpxPointToPositionConverter;
use App\Criticalmass\Geo\Entity\Position;
use PHPUnit\Framework\TestCase;

class ExtendedGpxPointsTest extends TestCase
{
    public function testConverterLatLngAccuracyGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><vdop>12.3</vdop></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition->setAltitudeAccuracy(12.3);

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngAltitudeAccuracyGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><vdop>12.3</vdop></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition->setAltitudeAccuracy(12.3);

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngAltitudeAltitudeAccuracyGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><ele>42.3</ele><vdop>12.3</vdop></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition
            ->setAltitude(42.3)
            ->setAltitudeAccuracy(12.3);

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngSpeedGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><extensions><speed>99.5</speed></extensions></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition->setSpeed(99.5);

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngHeadingGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><extensions><heading>359.9</heading></extensions></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition->setHeading(359.9);

        $this->assertEquals($expectedPosition, $actualPosition);
    }
}