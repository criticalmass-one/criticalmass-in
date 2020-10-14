<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\Converter;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;

class PositionToXmlTrackPointConverter
{
    private function __construct()
    {

    }

    public static function convert(PositionInterface $position, \XMLWriter $writer): \XMLWriter
    {
        $writer->startElement('trkpt');

        if ($position->getLatitude() && $position->getLongitude()) {
            $writer->writeAttribute('lat', (string) $position->getLatitude());
            $writer->writeAttribute('lon', (string) $position->getLongitude());
        }

        if ($position->getAltitude()) {
            $writer->startElement('ele');
            $writer->text((string) $position->getAltitude());
            $writer->endElement();
        }

        if ($dateTime = $position->getDateTime()) {
            $writer->startElement('time');
            $writer->text(self::formatDateTime($dateTime));
            $writer->endElement();
        }

        $writer->endElement();

        return $writer;
    }

    protected static function formatDateTime(\DateTime $dateTime): string
    {
        $dateTimeSpec = '%sT%sZ';

        return sprintf($dateTimeSpec, $dateTime->format('Y-m-d'), $dateTime->format('H:i:s'));
    }
}