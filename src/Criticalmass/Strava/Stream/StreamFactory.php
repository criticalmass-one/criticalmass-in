<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Stream;

class StreamFactory
{
    public static function build(\stdClass $streams): StreamList
    {
        $streamList = new StreamList();

        /** @var \stdClass $streamData */
        foreach ($streams as $type => $streamData) {
            $stream = new Stream();
            $stream
                ->setType($type)
                ->setSeriesType($streamData->series_type)
                ->setResolution($streamData->resolution)
                ->setOriginalSize($streamData->original_size)
                ->setData($streamData->data);

            $streamList->addStream($stream->getType(), $stream);
        }

        return $streamList;
    }
}