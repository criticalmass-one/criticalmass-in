<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\DoctrineQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="rideType")
 */
class RideTypeQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    protected string $rideType;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="rideType")
     */
    public function setRideType(string $rideType): self
    {
        $this->rideType = $rideType;

        return $this;
    }

    public function getRideType(): string
    {
        return $this->rideType;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['rideType' => strtoupper($this->rideType)]);
    }
}
