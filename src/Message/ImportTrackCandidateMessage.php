<?php declare(strict_types=1);

namespace App\Message;

use Carbon\Carbon;

class ImportTrackCandidateMessage
{
    public function __construct(
        private readonly int $userId,
        private readonly int $activityId,
        private readonly string $name,
        private readonly float $distance,
        private readonly int $elapsedTime,
        private readonly string $type,
        private readonly Carbon $startDateTime,
        private readonly float $startLatitude,
        private readonly float $startLongitude,
        private readonly float $endLatitude,
        private readonly float $endLongitude,
        private readonly string $polyline
    ) {
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getActivityId(): int
    {
        return $this->activityId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDistance(): float
    {
        return $this->distance;
    }

    public function getElapsedTime(): int
    {
        return $this->elapsedTime;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getStartDateTime(): Carbon
    {
        return $this->startDateTime;
    }

    public function getStartLatitude(): float
    {
        return $this->startLatitude;
    }

    public function getStartLongitude(): float
    {
        return $this->startLongitude;
    }

    public function getEndLatitude(): float
    {
        return $this->endLatitude;
    }

    public function getEndLongitude(): float
    {
        return $this->endLongitude;
    }

    public function getPolyline(): string
    {
        return $this->polyline;
    }
}
