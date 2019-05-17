<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxWriter;

use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\PositionList\PositionListInterface;

class GpxWriter implements GpxWriterInterface
{
    /** @var PositionListInterface $positionList */
    protected $positionList;

    /** @var array $gpxAttributes */
    protected $gpxAttributes = [];

    /** @var string $gpxContent */
    protected $gpxContent = null;

    /** @var \XMLWriter $writer */
    protected $writer;

    public function __construct()
    {
        $this->writer = new \XMLWriter();
    }

    public function setPositionList(PositionListInterface $positionList): GpxWriterInterface
    {
        $this->positionList = $positionList;

        return $this;
    }

    public function getGpxContent(): string
    {
        return $this->gpxContent;
    }

    public function saveGpxContent(string $filename): void
    {
        file_put_contents($filename, $this->gpxContent);
    }

    public function addGpxAttribute(string $attributeName, string $attributeValue): GpxWriterInterface
    {
        $this->gpxAttributes[$attributeName] = $attributeValue;

        return $this;
    }

    public function addStandardGpxAttributes(): GpxWriterInterface
    {
        $this->gpxAttributes['xmlns'] = 'http://www.topografix.com/GPX/1/1';
        $this->gpxAttributes['xmlns:xsi'] = 'http://www.w3.org/2001/XMLSchema-instance';
        $this->gpxAttributes['xsi:schemaLocation'] = 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd';

        return $this;
    }

    public function generateGpxContent(): void
    {
        $this->writer->openMemory();
        $this->writer->startDocument('1.0');

        $this->writer->setIndent(true);

        $this->writer->startElement('gpx');

        $this->generateGpxAttributes();
        $this->generateGpxMetadata();

        $this->writer->startElement('trk');
        $this->writer->startElement('trkseg');

        /** @var PositionInterface $position */
        for ($n = 0; $n < $this->positionList->count(); ++$n) {
            $this->generateGpxPosition($this->positionList->get($n));
        }

        $this->writer->endElement();
        $this->writer->endElement();
        $this->writer->endElement();
        $this->writer->endDocument();

        $this->gpxContent = $this->writer->outputMemory(true);

        $this->writer->flush();
    }

    protected function generateGpxAttributes(): GpxWriterInterface
    {
        foreach ($this->gpxAttributes as $attributeName => $attributeValue) {
            $this->writer->writeAttribute($attributeName, $attributeValue);
        }

        return $this;
    }

    protected function generateGpxMetadata(): GpxWriterInterface
    {
        if (count($this->positionList) > 0) {
            $this->writer->startElement('metadata');
            $this->writer->startElement('time');

            /** @var \DateTime $dateTime */
            $dateTime = $this->positionList->get(0)->getDateTime();
            $this->writer->text($dateTime->format('Y-m-d') . 'T' . $dateTime->format('H:i:s') . 'Z');

            $this->writer->endElement();
            $this->writer->endElement();
        }

        return $this;
    }

    protected function generateGpxPosition(PositionInterface $position): GpxWriterInterface
    {
        $this->writer->startElement('trkpt');
        $this->writer->writeAttribute('lat', (string) $position->getLatitude());
        $this->writer->writeAttribute('lon', (string) $position->getLongitude());

        if ($position->getAltitude()) {
            $this->writer->startElement('ele');
            $this->writer->text((string) $position->getAltitude());
            $this->writer->endElement();
        }

        if ($position->getDateTime()) {
            $this->writer->startElement('time');
            $dateTime = $position->getDateTime();
            $this->writer->text($dateTime->format('Y-m-d') . 'T' . $dateTime->format('H:i:s') . 'Z');
            $this->writer->endElement();
        }

        $this->writer->endElement();

        return $this;
    }
} 
