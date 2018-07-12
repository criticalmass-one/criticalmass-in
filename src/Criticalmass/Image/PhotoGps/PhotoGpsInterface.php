<?php

namespace AppBundle\Criticalmass\Image\PhotoGps;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Track;

interface PhotoGpsInterface
{
    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGpsInterface;
    public function setPhoto(Photo $photo): PhotoGpsInterface;
    public function getPhoto(): Photo;
    public function setTrack(Track $track = null): PhotoGpsInterface;
    public function execute(): PhotoGpsInterface;
}
