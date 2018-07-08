<?php declare(strict_types=1);

namespace AppBundle\Event\City;

use AppBundle\Entity\City;
use Symfony\Component\EventDispatcher\Event;

abstract class AbstractCityEvent extends Event
{
    /** @var City $city */
    protected $city;

    public function __construct(City $city)
    {
        $this->city = $city;
    }

    public function getCity(): City
    {
        return $this->city;
    }
}
