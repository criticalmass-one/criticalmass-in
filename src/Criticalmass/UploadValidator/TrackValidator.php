<?php declare(strict_types=1);

namespace App\Criticalmass\UploadValidator;

use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoDateTimeException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoLatitudeLongitudeException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NotEnoughCoordsException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoValidGpxStructureException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoXmlException;
use App\Entity\Track;
use Exception;
use League\Flysystem\FilesystemInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackValidator implements UploadValidatorInterface
{
    /** @var Track $track */
    protected $track;

    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper;

    /** @var FilesystemInterface $filesystem */
    protected $filesystem;

    /** @var \SimpleXMLElement $simpleXml */
    protected $simpleXml;

    /** @var string $rawFileContent */
    protected $rawFileContent;

    public function __construct(UploaderHelper $uploaderHelper, FilesystemInterface $filesystem)
    {
        $this->uploaderHelper = $uploaderHelper;
        $this->filesystem = $filesystem;
    }

    public function loadTrack(Track $track): TrackValidator
    {
        $this->track = $track;

        $filename = $this->uploaderHelper->asset($track, 'trackFile');

        $this->rawFileContent = $this->filesystem->read($filename);

        return $this;
    }

    protected function checkForXmlContent(): void
    {
        //echo "checkForXmlContent";
        try {
            $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
        } catch (Exception $e) {
            throw new NoXmlException();
        }
    }

    protected function checkForBasicGpxStructure(): void
    {
        //echo "checkForBasicGpxStructure";
        try {
            $this->simpleXml->trk->trkseg->trkpt[0];
        } catch (Exception $e) {
            throw new NoValidGpxStructureException();
        }
    }

    protected function checkNumberOfPoints(): void
    {
        $counter = 0;
        
        foreach ($this->simpleXml->trk->trkseg as $trkseg) {
            $counter += count($trkseg->trkpt);
        }

        if ($counter <= 50) {
            throw new NotEnoughCoordsException();
        }
    }

    protected function checkForLatitudeLongitude(): void
    {
        //echo "checkForLatitudeLongitude";
        foreach ($this->simpleXml->trk->trkseg->trkpt as $point) {
            /* @TODO This is really bullshit, but php refuses to get is_float or stuff like this working. Replace preg_match with a faster solution! */
            if (
                !$point['lat'] ||
                !$point['lon'] ||
                !preg_match('/^([-]?)([0-9]{1,3})\.([0-9]*)$/', (string) $point['lat']) ||
                !preg_match('/^([-]?)([0-9]{1,3})\.([0-9]*)$/', (string) $point['lon'])
            ) {
                throw new NoLatitudeLongitudeException();
            }
        }
    }

    protected function checkForDateTime(): void
    {
        //echo "checkForDateTime";
        foreach ($this->simpleXml->trk->trkseg->trkpt as $point) {
            if (!$point->time) {
                throw new NoDateTimeException();
            }

            try {
                $dateTime = new \DateTime((string) $point->time);
            } catch (Exception $e) {
                throw new NoDateTimeException();
            }
        }
    }

    public function validate(): bool
    {
        $this->checkForXmlContent();
        $this->checkForBasicGpxStructure();
        $this->checkNumberOfPoints();
        $this->checkForLatitudeLongitude();
        $this->checkForDateTime();

        return true;
    }
}
