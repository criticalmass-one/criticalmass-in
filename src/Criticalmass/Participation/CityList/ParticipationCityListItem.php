<?php declare(strict_types=1);

namespace App\Criticalmass\Participation\CityList;

use App\Entity\City;

class ParticipationCityListItem
{
    /** @var $city */
    protected $city;

    /** @var int $counter */
    protected $counter = 0;

    public function __construct(City $city, int $counter = 1)
    {
        $this->city = $city;
        $this->counter = $counter;
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