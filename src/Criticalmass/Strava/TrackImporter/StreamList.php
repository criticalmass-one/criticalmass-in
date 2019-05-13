<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

class StreamList
{
    protected $streamList;

    public function getStreamList(): array
    {
        return $this->streamList;
    }

    public function setStreamList(array $streamList): StreamList
    {
        $this->streamList = $streamList;

        return $this;
    }
}