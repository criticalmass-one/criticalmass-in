<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Stream;

class StreamFactory
{
    public static function build(array $streamsArray): StreamList
    {
        $streamList = new StreamList();

        /** @var array $streamArray */
        foreach ($streamsArray as $streamArray) {
            $stream = new Stream();
            $stream
                ->setType($streamArray['type'])
                ->setSeriesType($streamArray['series_type'])
                ->setResolution($streamArray['resolution'])
                ->setOriginalSize($streamArray['original_size'])
                ->setData($streamArray['data']);

            $streamList->addStream($stream->getType(), $stream);
        }

        return $streamList;
    }
}