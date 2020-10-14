<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoGps;

use App\Entity\Photo;
use App\Entity\Track;

interface PhotoGpsInterface
{
    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGpsInterface;
    public function setPhoto(Photo $photo): PhotoGpsInterface;
    public function getPhoto(): Photo;
    public function setTrack(Track $track = null): PhotoGpsInterface;
    public function execute(): PhotoGpsInterface;
}
