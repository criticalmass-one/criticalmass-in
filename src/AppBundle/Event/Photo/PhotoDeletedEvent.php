<?php declare(strict_types=1);

namespace AppBundle\Event\Photo;

class PhotoDeletedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.deleted';
}
