<?php declare(strict_types=1);

namespace AppBundle\Event\Photo;

use AppBundle\Entity\Photo;
use Symfony\Component\EventDispatcher\Event;

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
