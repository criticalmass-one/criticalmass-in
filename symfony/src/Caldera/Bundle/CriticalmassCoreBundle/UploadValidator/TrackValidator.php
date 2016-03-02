<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\UploadValidator;

use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException\NoDateTimeException;
use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException\NoLatitudeLongitudeException;
use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException\NotEnoughCoordsException;
use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException\NoValidGpxStructureException;
use Caldera\Bundle\CriticalmassCoreBundle\UploadValidator\UploadValidatorException\TrackValidatorException\NoXmlException;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Exception;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackValidator implements UploadValidatorInterface
{
    /**
     * @var Track $track
     */
    protected $track;

    /**
     * @var UploaderHelper $uploaderHelper
     */
    protected $uploaderHelper;

    protected $rootDirectory;

    protected $simpleXml;

    protected $rawFileContent;

    public function __construct(UploaderHelper $uploaderHelper, $rootDirectory)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->rootDirectory = $rootDirectory.'/../web';
    }

    public function loadTrack(Track $track)
    {
        $this->track = $track;

        $filename = $this->uploaderHelper->asset($track, 'trackFile');

        $this->rawFileContent = file_get_contents($this->rootDirectory.$filename);
    }

    protected function checkForXmlContent()
    {
        //echo "checkForXmlContent";
        try {
            $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
        } catch (Exception $e) {
            throw new NoXmlException();
        }
    }

    protected function checkForBasicGpxStructure()
    {
        //echo "checkForBasicGpxStructure";
        try {
            $this->simpleXml->trk->trkseg->trkpt[0];
        } catch (Exception $e) {
            throw new NoValidGpxStructureException();
        }
    }

    protected function checkNumberOfPoints()
    {
        //echo "checkNumberOfPoints";
        if (count($this->simpleXml->trk->trkseg->trkpt) <= 50) {
            throw new NotEnoughCoordsException();
        }
    }

    protected function checkForLatitudeLongitude()
    {
        //echo "checkForLatitudeLongitude";
        foreach ($this->simpleXml->trk->trkseg->trkpt as $point) {
            /* @TODO This is really bullshit, but php refuses to get is_float or stuff like this working. Replace preg_match with a faster solution! */
            if (
                !$point['lat'] or
                !$point['lon'] or
                !preg_match('/^([-]?)([0-9]{1,3})\.([0-9]*)$/', $point['lat']) or
                !preg_match('/^([-]?)([0-9]{1,3})\.([0-9]*)$/', $point['lon'])
            ) {
                throw new NoLatitudeLongitudeException();
            }
        }
    }

    protected function checkForDateTime()
    {
        //echo "checkForDateTime";
        foreach ($this->simpleXml->trk->trkseg->trkpt as $point) {
            if (!$point->time) {
                throw new NoDateTimeException();
            }

            try {
                $dateTime = new \DateTime($point->time);
            } catch (Exception $e) {
                throw new NoDateTimeException();
            }
        }
    }

    public function validate()
    {
        $this->checkForXmlContent();
        $this->checkForBasicGpxStructure();
        $this->checkNumberOfPoints();
        $this->checkForLatitudeLongitude();
        $this->checkForDateTime();

        return true;
    }
}