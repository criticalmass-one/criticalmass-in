<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;

class PhotoUploadedEvent extends AbstractPhotoEvent
{
    final const NAME = 'photo.uploaded';

    public function __construct(Photo $photo, protected bool $flush = true, protected string $tmpFilename = null)
    {
        parent::__construct($photo);
    }

    public function isFlush(): bool
    {
        return $this->flush;
    }

    public function getTmpFilename(): ?string
    {
        return $this->tmpFilename;
    }
}
