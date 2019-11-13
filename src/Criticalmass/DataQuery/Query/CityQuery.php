<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Entity\City;
use Elastica\Query\AbstractQuery;

class CityQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /** @var City $city */
    protected $city;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="citySlug")
     */
    public function setCity(City $city): CityQuery
    {
        $this->region = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function createElasticQuery(): AbstractQuery
    {
        return new \Elastica\Query\Term(['city' => $this->city->getId()]);
    }
}
