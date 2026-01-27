<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoGps;

use Carbon\Carbon;
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
        if ($dateTime = $this->getExifDateTime()) {
            $point = $this->gpxService->findPointAtTime($this->track, $dateTime, $this->dateTimeZone);

            if ($point) {
                $this->photo
                    ->setLatitude($point->latitude)
                    ->setLongitude($point->longitude);
            }
        }

        return $this;
    }

    protected function getExifDateTime(): ?Carbon
    {
        $exif = $this->readExifData();

        if ($exif && $dateTime = $exif->getCreationDate()) {
            $dateTime = Carbon::parse(sprintf($dateTime->format('Y-m-d H:i:s')), new \DateTimeZone('Europe/Berlin'));

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
