<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;

class PhotoUpdatedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.updated';

    /** @var bool $flush */
    protected $flush;

    public function __construct(Photo $photo, bool $flush = true)
    {
        $this->flush = $flush;

        parent::__construct($photo);
    }

    public function isFlush(): bool
    {
        return $this->flush;
    }
}
