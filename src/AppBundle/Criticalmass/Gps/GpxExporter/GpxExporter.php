<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Gps\GpxExporter;

use AppBundle\Entity\Position;

/** @deprecated */
class GpxExporter extends AbstractGpxExporter
{
    protected function generateGpxContent(): GpxExporterInterface
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0');

        $writer->setIndent(4);

        $writer->startElement('gpx');
        $writer->writeAttribute('creator', 'criticalmass.in');
        $writer->writeAttribute('version', '0.1');
        $writer->writeAttribute('xmlns', 'http://www.topografix.com/GPX/1/1');
        $writer->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $writer->writeAttribute('xsi:schemaLocation',
            'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd');

        $writer->startElement('metadata');
        $writer->startElement('time');

        $dateTime = $this->positionArray[0]->getCreationDateTime();
        $writer->text($dateTime->format('Y-m-d') . 'T' . $dateTime->format('H:i:s') . 'Z');

        $writer->endElement();
        $writer->endElement();

        $writer->startElement('trk');
        $writer->startElement('trkseg');

        /**
         * @var Position $position
         */
        foreach ($this->positionArray as $position) {
            $writer->startElement('trkpt');
            $writer->writeAttribute('lat', $position->getLatitude());
            $writer->writeAttribute('lon', $position->getLongitude());

            $writer->startElement('ele');
            $writer->text($position->getAltitude());
            $writer->endElement();

            $writer->startElement('time');

            $dateTime = new \DateTime();
            $dateTime->setTimestamp($position->getTimestamp());

            $writer->text($dateTime->format('Y-m-d') . 'T' . $dateTime->format('H:i:s') . 'Z');

            $writer->endElement();
            $writer->endElement();
        }

        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $this->gpxContent = $writer->outputMemory(true);

        return $this;
    }
} 
