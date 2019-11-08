<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\City;

class CityQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /** @var City $city */
    protected $city;

    public function __construct(City $city)
    {
        $this->region = $city;
    }

    public function getCity(): City
    {
        return $this->city;
    }
}
