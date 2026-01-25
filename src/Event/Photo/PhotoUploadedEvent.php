<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;

class PhotoUploadedEvent extends AbstractPhotoEvent
{
    const NAME = 'photo.uploaded';

    /** @var bool $flush */
    protected $flush;

    /** @var string $tmpFilename */
    protected $tmpFilename;

    public function __construct(Photo $photo, bool $flush = true, ?string $tmpFilename = null)
    {
        $this->flush = $flush;
        $this->tmpFilename = $tmpFilename;

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
