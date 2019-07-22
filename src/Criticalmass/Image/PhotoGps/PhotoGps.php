<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoGps;

use App\Criticalmass\Geo\Converter\TrackToPositionListConverter;
use App\Criticalmass\Geo\Loop\Loop;
use PHPExif\Exif;

class PhotoGps extends AbstractPhotoGps
{
    public function execute(): PhotoGpsInterface
    {
        if ($gps = $this->getExifCoords()) {
            $this->photo
                ->setLatitude($gps['lat'])
                ->setLongitude($gps['lon']);
        } elseif ($this->track) {
            $this->approximateCoordinates();
        }

        return $this;
    }

    protected function approximateCoordinates(): PhotoGps
    {
        $this->trackReader->loadTrack($this->track);

        if ($dateTime = $this->getExifDateTime()) {
            $converter = new TrackToPositionListConverter($this->trackReader);
            $positionList = $converter->convert($this->track);

            $position = $this->loop
                ->setDateTimeZone($this->dateTimeZone)
                ->setPositionList($positionList)
                ->searchPositionForDateTime($dateTime);

            if ($position) {
                $this->photo
                    ->setLatitude($position->getLatitude())
                    ->setLongitude($position->getLongitude());
            }
        }

        return $this;
    }

    protected function getExifDateTime(): ?\DateTime
    {
        $exif = $this->readExifData();

        if ($exif && $dateTime = $exif->getCreationDate()) {
            $dateTime = new \DateTime(sprintf($dateTime->format('Y-m-d H:i:s')), new \DateTimeZone('Europe/Berlin'));

            $dateTime->setTimezone(new \DateTimeZone('UTC'))->getTimezone();

            return $dateTime;
        }

        return null;
    }

    protected function getExifCoords(): ?array
    {
        $exif = $this->readExifData();

        if ($exif && $gps = $exif->getGPS()) {
            if (is_string($gps)) {
                list($lat, $lon) = explode(',', $gps);

                $gps = [
                    'lat' => (float) $lat,
                    'lon' => (float) $lon,
                ];
            }

            return $gps;
        }

        return null;
    }

    protected function readExifData(): ?Exif
    {
        return $this->exifWrapper->getExifData($this->photo);
    }
}
