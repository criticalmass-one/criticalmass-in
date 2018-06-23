<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Event\Track;

use Criticalmass\Bundle\AppBundle\Entity\Track;
use Symfony\Component\EventDispatcher\Event;

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
