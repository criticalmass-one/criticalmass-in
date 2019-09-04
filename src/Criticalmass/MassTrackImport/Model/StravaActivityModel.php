<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Model;

use Caldera\GeoBasic\Coord\CoordInterface;

class StravaActivityModel
{
    /** @var int $id */
    protected $id;

    /** @var string $name */
    protected $name;

    /** @var float $distance */
    protected $distance;

    /** @var int $elapsedTime */
    protected $elapsedTime;

    /** @var string $type */
    protected $type;

    /** @var \DateTime $startDateTime */
    protected $startDateTime;

    /** @var CoordInterface $startCoord */
    protected $startCoord;

    /** @var CoordInterface $endCoord */
    protected $endCoord;

    /** @var string $polyline */
    protected $polyline;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): StravaActivityModel
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): StravaActivityModel
    {
        $this->name = $name;

        return $this;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function setDistance(float $distance): StravaActivityModel
    {
        $this->distance = $distance;

        return $this;
    }

    public function getElapsedTime(): int
    {
        return $this->elapsedTime;
    }

    public function setElapsedTime(int $elapsedTime): StravaActivityModel
    {
        $this->elapsedTime = $elapsedTime;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): StravaActivityModel
    {
        $this->type = $type;

        return $this;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }

    public function setStartDateTime(\DateTime $startDateTime): StravaActivityModel
    {
        $this->startDateTime = $startDateTime;

        return $this;
    }

    public function getStartCoord(): CoordInterface
    {
        return $this->startCoord;
    }

    public function setStartCoord(CoordInterface $startCoord): StravaActivityModel
    {
        $this->startCoord = $startCoord;

        return $this;
    }

    public function getEndCoord(): CoordInterface
    {
        return $this->endCoord;
    }

    public function setEndCoord(CoordInterface $endCoord): StravaActivityModel
    {
        $this->endCoord = $endCoord;

        return $this;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }

    public function setPolyline(string $polyline): StravaActivityModel
    {
        $this->polyline = $polyline;

        return $this;
    }
}
