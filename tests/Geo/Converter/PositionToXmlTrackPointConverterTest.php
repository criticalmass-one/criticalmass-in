<?php declare(strict_types=1);

namespace Tests\Geo\Converter;

use App\Criticalmass\Geo\Converter\PositionToXmlTrackPointConverter;
use App\Criticalmass\Geo\Entity\Position;
use PHPUnit\Framework\TestCase;

class PositionToXmlTrackPointConverterTest extends TestCase
{
    public function testLatLngPosition(): void
    {
        $position = new Position(57.5, 10.5);
        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0');

        PositionToXmlTrackPointConverter::convert($position, $xmlWriter);

        $actualContent = $xmlWriter->outputMemory(true);

        $xmlWriter->flush();

        $expectedContent = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"/>';

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testLatLngDateTimePosition(): void
    {
        $position = new Position(57.5, 10.5);
        $position->setDateTime(new \DateTime('2011-06-24 19:00:00'));

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0');

        PositionToXmlTrackPointConverter::convert($position, $xmlWriter);

        $actualContent = $xmlWriter->outputMemory(true);

        $xmlWriter->flush();

        $expectedContent = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><time>2011-06-24T19:00:00Z</time></trkpt>';

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testLatLngAltitudePosition(): void
    {
        $position = new Position(57.5, 10.5);
        $position->setAltitude(42.3);

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0');

        PositionToXmlTrackPointConverter::convert($position, $xmlWriter);

        $actualContent = $xmlWriter->outputMemory(true);

        $xmlWriter->flush();

        $expectedContent = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><ele>42.3</ele></trkpt>';

        $this->assertEquals($expectedContent, $actualContent);
    }

    public function testLatLngDateTimeAltitudePosition(): void
    {
        $position = new Position(57.5, 10.5);
        $position
            ->setDateTime(new \DateTime('2011-06-24 19:00:00'))
            ->setAltitude(42.3);

        $xmlWriter = new \XMLWriter();
        $xmlWriter->openMemory();
        $xmlWriter->startDocument('1.0');

        PositionToXmlTrackPointConverter::convert($position, $xmlWriter);

        $actualContent = $xmlWriter->outputMemory(true);

        $xmlWriter->flush();

        $expectedContent = '<?xml version="1.0"?>
<trkpt lat="57.5" lon="10.5"><ele>42.3</ele><time>2011-06-24T19:00:00Z</time></trkpt>';

        $this->assertEquals($expectedContent, $actualContent);
    }

}
