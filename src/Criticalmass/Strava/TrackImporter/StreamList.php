<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\TrackImporter;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class StreamList
{
    /**
     * @var array $streamList
     * @JMS\Expose
     * @JMS\Type("Array>Stream>")
     */
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