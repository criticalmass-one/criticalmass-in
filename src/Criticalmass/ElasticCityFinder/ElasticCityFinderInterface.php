<?php declare(strict_types=1);

namespace App\Criticalmass\ElasticCityFinder;

use App\Entity\City;
use Elastica\Query;

interface ElasticCityFinderInterface
{
    public function findNearCities(City $city, int $distance = 50): array;
    public function createQuery(City $city, int $size = 15, int $distance = 50): Query;
}