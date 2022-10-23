<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\CityList;

use App\Entity\City;

class ParticipationCityListItem
{
    public function __construct(
        /** @var $city */
        protected City $city,
        protected int $counter = 1
    )
    {
    }

    public function getCity()
    {
        return $this->city;
    }

    public function incCounter(int $steps = 1): ParticipationCityListItem
    {
        $this->counter += $steps;

        return $this;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }
}