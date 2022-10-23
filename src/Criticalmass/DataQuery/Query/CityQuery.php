<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use App\Entity\City;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\DoctrineQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="slug")
 */
class CityQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("App\Entity\City")
     * @var City $city
     */
    protected $city;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="citySlug")
     */
    public function setCity(City $city): CityQuery
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['city' => $this->city->getCity()]);
    }

    public function isOverridenBy(): array
    {
        return [
            RideQuery::class,
        ];
    }
}
