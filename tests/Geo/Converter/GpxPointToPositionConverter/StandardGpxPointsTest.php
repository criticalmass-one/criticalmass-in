<?php declare(strict_types=1);

namespace Tests\Geo\Converter\GpxPointToPositionConverter;

use App\Criticalmass\Geo\Converter\GpxPointToPositionConverter;
use App\Criticalmass\Geo\Entity\Position;
use PHPUnit\Framework\TestCase;

class StandardGpxPointsTest extends TestCase
{
    public function testConverterLatLngGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5" />';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngAltitudeGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><ele>42.3</ele></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition->setAltitude(42.3);

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngDateTimeGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><time>2011-06-24T19:00:00Z</time></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition->setDateTime(new \DateTime('2011-06-24 19:00:00', new \DateTimeZone('UTC')));

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function testConverterLatLngAltitudeDateTimeGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><ele>42.3</ele><time>2011-06-24T19:00:00Z</time></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $actualPosition = GpxPointToPositionConverter::convert($xmlGpx);

        $expectedPosition = new Position(57.5, 10.5);
        $expectedPosition
            ->setAltitude(42.3)
            ->setDateTime(new \DateTime('2011-06-24 19:00:00', new \DateTimeZone('UTC')));

        $this->assertEquals($expectedPosition, $actualPosition);
    }
}