<?php declare(strict_types=1);

namespace App\Criticalmass\Fit\FitParser;

class FitData
{
    public function __construct(
        private readonly array $latLngData,
        private readonly array $altitudeData,
        private readonly array $timeData,
        private readonly \DateTime $startDateTime,
    ) {
    }

    public function getLatLngData(): array
    {
        return $this->latLngData;
    }

    public function getAltitudeData(): array
    {
        return $this->altitudeData;
    }

    public function getTimeData(): array
    {
        return $this->timeData;
    }

    public function getStartDateTime(): \DateTime
    {
        return $this->startDateTime;
    }
}
