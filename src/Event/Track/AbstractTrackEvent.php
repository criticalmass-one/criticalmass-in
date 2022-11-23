<?php declare(strict_types=1);

namespace App\Event\Track;

use App\Entity\Track;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractTrackEvent extends Event
{
    /** @var Track $track */
    protected $track;

    public function __construct(Track $track)
    {
        $this->track = $track;
    }

    public function getTrack(): Track
    {
        return $this->track;
    }
}
