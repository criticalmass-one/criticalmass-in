<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\Model;

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

    /** @var \DateTime $startDate */
    protected $startDate;

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

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): StravaActivityModel
    {
        $this->startDate = $startDate;

        return $this;
    }
}
