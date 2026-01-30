<?php declare(strict_types=1);

namespace App\Criticalmass\UploadValidator;

use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\InvalidFitFileException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoDateTimeException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoLatitudeLongitudeException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NotEnoughCoordsException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoValidGpxStructureException;
use App\Criticalmass\UploadValidator\UploadValidatorException\TrackValidatorException\NoXmlException;
use App\Entity\Track;
use Exception;
use League\Flysystem\FilesystemOperator;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackValidator implements UploadValidatorInterface
{
    private const FIT_HEADER_SIGNATURE = '.FIT';
    private const FIT_MIN_FILE_SIZE = 12;

    /** @var Track $track */
    protected $track;

    /** @var \SimpleXMLElement $simpleXml */
    protected $simpleXml;

    /** @var string $rawFileContent */
    protected $rawFileContent;

    public function __construct(
        private readonly UploaderHelper $uploaderHelper,
        private readonly FilesystemOperator $filesystem)
    {

    }

    public function loadTrack(Track $track): TrackValidator
    {
        $this->track = $track;

        $filename = $this->uploaderHelper->asset($track, 'trackFile');

        $this->rawFileContent = $this->filesystem->read($filename);

        return $this;
    }

    public function isFitFile(): bool
    {
        $filename = $this->track->getTrackFilename();

        if ($filename && str_ends_with(strtolower($filename), '.fit')) {
            return true;
        }

        if (strlen($this->rawFileContent) >= self::FIT_MIN_FILE_SIZE) {
            $headerSignature = substr($this->rawFileContent, 8, 4);

            if ($headerSignature === self::FIT_HEADER_SIGNATURE) {
                return true;
            }
        }

        return false;
    }

    protected function validateFitFile(): void
    {
        if (strlen($this->rawFileContent) < self::FIT_MIN_FILE_SIZE) {
            throw new InvalidFitFileException('FIT file is too small to be valid.');
        }

        $headerSignature = substr($this->rawFileContent, 8, 4);

        if ($headerSignature !== self::FIT_HEADER_SIGNATURE) {
            throw new InvalidFitFileException('File does not contain a valid FIT header signature.');
        }
    }

    protected function checkForXmlContent(): void
    {
        try {
            $this->simpleXml = new \SimpleXMLElement($this->rawFileContent);
        } catch (Exception $e) {
            throw new NoXmlException();
        }
    }

    protected function checkForBasicGpxStructure(): void
    {
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
        foreach ($this->simpleXml->trk->trkseg->trkpt as $point) {
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
        if ($this->isFitFile()) {
            $this->validateFitFile();

            return true;
        }

        $this->checkForXmlContent();
        $this->checkForBasicGpxStructure();
        $this->checkNumberOfPoints();
        $this->checkForLatitudeLongitude();
        $this->checkForDateTime();

        return true;
    }
}
