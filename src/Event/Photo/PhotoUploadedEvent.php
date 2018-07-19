<?php declare(strict_types=1);

namespace App\Event\Photo;

class PhotoUploadedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.uploaded';
}
