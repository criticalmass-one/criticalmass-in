<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Gps\GpxExporter;


use Caldera\Bundle\CriticalmassModelBundle\Entity\CriticalmapsUser;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Position;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ticket;

class GpxExporter
{
    protected $entityManager;
    protected $doctrine;
    protected $ticket = null;
    protected $criticalmapsUser = null;
    protected $positionArray;
    protected $gpxContent = null;

    public function __construct($entityManager, $doctrine)
    {
        $this->entityManager = $entityManager;
        $this->doctrine = $doctrine;
    }

    public function setTicket(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function setCriticalmapsUser(CriticalmapsUser $criticalmapsUser)
    {
        $this->criticalmapsUser = $criticalmapsUser;
    }

    protected function findPositions()
    {
        if ($this->ticket) {
            $this->positionArray = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Position')->findPositionsForTicket($this->ticket);
        }

        if ($this->criticalmapsUser) {
            $this->positionArray = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Position')->findPositionsForCriticalmapsUser($this->criticalmapsUser);
        }
    }

    protected function generateGpxContent()
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
        $writer->writeAttribute('xsi:schemaLocation', 'http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtensionv1.xsd');

        $writer->startElement('metadata');
        $writer->startElement('time');

        $dateTime = $this->positionArray[0]->getCreationDateTime();
        $writer->text($dateTime->format('Y-m-d').'T'.$dateTime->format('H:i:s').'Z');

        $writer->endElement();
        $writer->endElement();

        $writer->startElement('trk');
        $writer->startElement('trkseg');

        /**
         * @var Position $position
         */
        foreach ($this->positionArray as $position)
        {
            $writer->startElement('trkpt');
            $writer->writeAttribute('lat', $position->getLatitude());
            $writer->writeAttribute('lon', $position->getLongitude());

            $writer->startElement('ele');
            $writer->text($position->getAltitude());
            $writer->endElement();

            $writer->startElement('time');

            /* Well, only positions provided by Glympse or external tools provide the timestamp of a position.
             * Critical Maps only provides latitude and longitude, so we need to generate the timestamp ourself.
             */
            if ($position->getCriticalmapsUser()) {
                $dateTime = $position->getCreationDateTime();
            } else {
                $dateTime = new \DateTime();
                $dateTime->setTimestamp($position->getTimestamp());
            }


            $writer->text($dateTime->format('Y-m-d').'T'.$dateTime->format('H:i:s').'Z');

            $writer->endElement();
            $writer->endElement();
        }

        $writer->endElement();
        $writer->endElement();
        $writer->endElement();
        $writer->endDocument();
        $this->gpxContent = $writer->outputMemory(true);
    }

    public function execute()
    {
        $this->findPositions();

        if (count($this->positionArray) > 0) {
            $this->generateGpxContent();
        }
    }

    public function getGpxContent()
    {
        return $this->gpxContent;
    }
} 