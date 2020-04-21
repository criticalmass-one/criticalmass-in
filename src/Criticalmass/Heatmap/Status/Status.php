<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Status;

class Status
{
    /** @var int $maxTracks */
    protected $maxTracks = 0;

    /** @var $paintedTracks */
    protected $paintedTracks = 0;

    /** @var $zoomLevel */
    protected $zoomLevel = 0;

    /** @var int $maxTiles */
    protected $maxTiles = 0;

    /** @var int $paintedTiles */
    protected $paintedTiles = 0;

    /** @var int $memoryUsage */
    protected $memoryUsage = 0;

    public function __construct(int $maxTracks)
    {
        $this->maxTracks = $maxTracks;
    }

    public function resetPaintedTiles(): Status
    {
        $this->paintedTiles = 0;

        return $this;
    }

    public function setMaxTiles(int $maxTiles): Status
    {
        $this->maxTiles = $maxTiles;

        return $this;
    }

    public function setZoomLevel(int $zoomLevel): Status
    {
        $this->zoomLevel = $zoomLevel;

        return $this;
    }

    public function incPaintedTiles(): Status
    {
        ++$this->paintedTiles;

        return $this;
    }

    public function incPaintedTracks(): Status
    {
        ++$this->paintedTracks;

        return $this;
    }

    public function getMaxTracks(): int
    {
        return $this->maxTracks;
    }

    public function getPaintedTracks()
    {
        return $this->paintedTracks;
    }

    public function getZoomLevel()
    {
        return $this->zoomLevel;
    }

    public function getMaxTiles(): int
    {
        return $this->maxTiles;
    }

    public function getPaintedTiles(): int
    {
        return $this->paintedTiles;
    }

    public function getMemoryUsage(): int
    {
        return $this->memoryUsage;
    }

    public function setMemoryUsage(int $memoryUsage): Status
    {
        $this->memoryUsage = $memoryUsage;

        return $this;
    }
}
