<?php declare(strict_types=1);

namespace App\Event\Photo;

class PhotoUpdatedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.updated';
}
