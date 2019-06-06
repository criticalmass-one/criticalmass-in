<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\GpxWriter;

use App\Criticalmass\Geo\Converter\PositionToXmlTrackPointConverter;
use App\Criticalmass\Geo\EntityInterface\PositionInterface;
use App\Criticalmass\Geo\PositionList\PositionList;
use App\Criticalmass\Geo\PositionList\PositionListInterface;
use League\Flysystem\FilesystemInterface;

class GpxWriter implements GpxWriterInterface
{
    /** @var PositionListInterface $positionList */
    protected $positionList;

    /** @var array $gpxAttributes */
    protected $gpxAttributes = [];

    /** @var string $gpxContent */
    protected $gpxContent = '';

    /** @var \XMLWriter $writer */
    protected $writer;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->writer = new \XMLWriter();
        $this->positionList = new PositionList();
        $this->filesystem = $filesystem;
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
        if ($this->gpxContent) {
            $this->filesystem->put($filename, $this->gpxContent);
        }
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

    public function generateGpxContent(): GpxWriterInterface
    {
        $this->writer->openMemory();
        $this->writer->startDocument('1.0');

        $this->writer->setIndent(true);

        $this->writer->startElement('gpx');

        $this->addStandardGpxAttributes();
        $this->generateGpxAttributes();
        $this->generateGpxMetadata();

        $this->writer->startElement('trk');
        $this->writer->startElement('trkseg');

        /** @var PositionInterface $position */
        for ($n = 0; $n < $this->positionList->count(); ++$n) {
            $position = $this->positionList->get($n);

            PositionToXmlTrackPointConverter::convert($position, $this->writer);
        }

        $this->writer->endElement();
        $this->writer->endElement();
        $this->writer->endElement();
        $this->writer->endDocument();

        $this->gpxContent = $this->writer->outputMemory(true);

        $this->writer->flush();

        return $this;
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
        if (count($this->positionList) === 0) {
            return $this;
        }

        if (!$this->positionList->get(0)->getDateTime()) {
            return $this;
        }

        $this->writer->startElement('metadata');
        $this->writer->startElement('time');

        /** @var \DateTime $dateTime */
        $dateTime = $this->positionList->get(0)->getDateTime();
        $this->writer->text($dateTime->format('Y-m-d') . 'T' . $dateTime->format('H:i:s') . 'Z');

        $this->writer->endElement();
        $this->writer->endElement();

        return $this;
    }
} 
