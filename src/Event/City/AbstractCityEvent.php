<?php declare(strict_types=1);

namespace App\Event\City;

use App\Entity\City;
use Symfony\Contracts\EventDispatcher\Event;

abstract class AbstractCityEvent extends Event
{
    public function __construct(protected City $city)
    {
    }

    public function getCity(): City
    {
        return $this->city;
    }
}
