<?php declare(strict_types=1);

namespace App\Event\Track;

use App\Entity\Track;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractTrackEvent extends Event
{
    public function __construct(protected Track $track)
    {
    }

    public function getTrack(): Track
    {
        return $this->track;
    }
}
