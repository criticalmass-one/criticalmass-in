<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;

class PhotoUploadedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.uploaded';

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
