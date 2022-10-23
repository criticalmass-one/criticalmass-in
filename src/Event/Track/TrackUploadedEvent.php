<?php declare(strict_types=1);

namespace App\Event\Track;

class TrackUploadedEvent extends AbstractTrackEvent
{
    final const NAME = 'track.uploaded';
}
