<?php

namespace App\Criticalmass\Image\PhotoGps;

use PHPExif\Exif;
use PHPExif\Reader\Reader;

/**
 * @deprecated
 */
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
            $result = $this->trackReader->findCoordNearDateTime($dateTime);

            $this->photo->setLatitude($result['latitude']);
            $this->photo->setLongitude($result['longitude']);
        }

        return $this;
    }

    protected function getExifDateTime(): ?\DateTime
    {
        $exif = $this->readExifData();

        if ($dateTime = $exif->getCreationDate()) {
            return $dateTime;
        }

        return null;
    }

    protected function getExifCoords(): ?array
    {
        $exif = $this->readExifData();

        if ($gps = $exif->getGPS()) {
            if (is_string($gps)) {
                list($lat, $lon) = explode(',', $gps);

                $gps = [
                    'lat' => $lat,
                    'lon' => $lon,
                ];
            }

            return $gps;
        }

        return null;
    }

    protected function readExifData(): Exif
    {
        $filename = sprintf('%s/%s', $this->uploadDestinationPhoto, $this->photo->getImageName());

        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->read($filename);

        return $exif;
    }
}
