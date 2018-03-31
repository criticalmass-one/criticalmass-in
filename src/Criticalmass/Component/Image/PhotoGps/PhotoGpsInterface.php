<?php

namespace Criticalmass\Component\Image\PhotoGps;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Track;

interface PhotoGpsInterface
{
    public function setDateTimeZone(\DateTimeZone $dateTimeZone = null): PhotoGpsInterface;
    public function setPhoto(Photo $photo): PhotoGpsInterface;
    public function getPhoto(): Photo;
    public function setTrack(Track $track = null): PhotoGpsInterface;
    public function execute(): PhotoGpsInterface;
}
