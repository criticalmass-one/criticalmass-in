<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractPhotoEvent extends Event
{
    public function __construct(protected Photo $photo)
    {
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }
}
