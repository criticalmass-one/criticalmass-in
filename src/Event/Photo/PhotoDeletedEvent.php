<?php declare(strict_types=1);

namespace App\Event\Photo;

class PhotoDeletedEvent extends AbstractPhotoEvent
{
    final const NAME = 'photo.deleted';
}
