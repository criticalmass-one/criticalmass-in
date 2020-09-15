<?php declare(strict_types=1);

namespace App\Criticalmass\Strava\Stream;

class Stream
{
    /** @var string $type */
    protected $type;

    /** @var string $seriesType */
    protected $seriesType;

    /** @var int $type */
    protected $originalSize;

    /** @var string $type */
    protected $resolution;

    /** @var array $type */
    protected $data;

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Stream
    {
        $this->type = $type;

        return $this;
    }

    public function getSeriesType(): string
    {
        return $this->seriesType;
    }

    public function setSeriesType(string $seriesType): Stream
    {
        $this->seriesType = $seriesType;

        return $this;
    }

    public function getOriginalSize(): int
    {
        return $this->originalSize;
    }

    public function setOriginalSize(int $originalSize): Stream
    {
        $this->originalSize = $originalSize;

        return $this;
    }

    public function getResolution(): string
    {
        return $this->resolution;
    }

    public function setResolution(string $resolution): Stream
    {
        $this->resolution = $resolution;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): Stream
    {
        $this->data = $data;

        return $this;
    }
}