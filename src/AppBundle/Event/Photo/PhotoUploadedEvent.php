<?php declare(strict_types=1);

namespace AppBundle\Event\Photo;

class PhotoUploadedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.uploaded';
}
