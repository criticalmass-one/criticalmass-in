<?php declare(strict_types=1);

namespace AppBundle\Event\Photo;

class PhotoUpdatedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.updated';
}
