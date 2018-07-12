<?php declare(strict_types=1);

namespace App\Event\Track;

class TrackDeletedEvent extends AbstractTrackEvent
{
    const NAME = 'track.deleted';
}
