<?php declare(strict_types=1);

namespace App\Event\Photo;

use App\Entity\Photo;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractPhotoEvent extends Event
{
    /** @var Photo $photo */
    protected $photo;

    public function __construct(Photo $photo)
    {
        $this->photo = $photo;
    }

    public function getPhoto(): Photo
    {
        return $this->photo;
    }
}
