<?php declare(strict_types=1);

namespace AppBundle\Event\Track;

class TrackDeletedEvent extends AbstractTrackEvent
{
    const NAME = 'track.deleted';
}
