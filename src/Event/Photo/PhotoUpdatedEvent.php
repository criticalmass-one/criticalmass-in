<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;

class PhotoUpdatedEvent extends AbstractPhotoEvent
{
    final const NAME = 'photo.updated';

    public function __construct(Photo $photo, protected bool $flush = true)
    {
        parent::__construct($photo);
    }

    public function isFlush(): bool
    {
        return $this->flush;
    }
}
