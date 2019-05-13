<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Stream;

class StreamList
{
    /** @var array $streamList */
    protected $streamList;

    /** @var int $length */
    protected $length = 0;

    public function getStreamList(): array
    {
        return $this->streamList;
    }

    public function getStream(string $key): Stream
    {
        return $this->streamList[$key];
    }

    public function setStreamList(array $streamList): StreamList
    {
        $this->streamList = $streamList;

        return $this;
    }

    public function addStream(string $type, Stream $stream): StreamList
    {
        $this->checkLength($stream);

        $this->streamList[$type] = $stream;

        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    protected function checkLength(Stream $stream): void
    {
        if ($stream->getOriginalSize() > $this->length) {
            $this->length = $stream->getOriginalSize();
        }
    }
}