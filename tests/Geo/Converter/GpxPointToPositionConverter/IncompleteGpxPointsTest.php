<?php declare(strict_types=1);

namespace Tests\Geo\Converter\GpxPointToPositionConverter;

use App\Criticalmass\Geo\Converter\GpxPointToPositionConverter;
use PHPUnit\Framework\TestCase;

class IncompleteGpxPointsTest extends TestCase
{
    public function testConverterEmptyGpx(): void
    {
        $gpxString = '<?xml version="1.0"?><trkpt />';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $position = GpxPointToPositionConverter::convert($xmlGpx);

        $this->assertNull($position);
    }

    public function testConverterAltitudeDateTimeGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt><ele>42.3</ele><time>2011-06-24T19:00:00Z</time></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $position = GpxPointToPositionConverter::convert($xmlGpx);

        $this->assertNull($position);
    }

    public function testConverterDateTimeGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt><time>2011-06-24T19:00:00Z</time></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $position = GpxPointToPositionConverter::convert($xmlGpx);

        $this->assertNull($position);
    }

    public function testConverterAltitudeGpx(): void
    {
        $gpxString = '<?xml version="1.0"?>
<trkpt><ele>42.3</ele></trkpt>';

        $xmlGpx = new \SimpleXMLElement($gpxString);

        $position = GpxPointToPositionConverter::convert($xmlGpx);

        $this->assertNull($position);
    }
}