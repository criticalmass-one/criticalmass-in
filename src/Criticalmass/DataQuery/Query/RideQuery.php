<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use App\Entity\Ride;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\DoctrineQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'slug')]
class RideQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    #[Constraints\Type(Ride::class)]
    protected Ride $ride;

    #[DataQuery\RequiredQueryParameter(parameterName: 'rideIdentifier')]
    public function setRide(Ride $ride): RideQuery
    {
        $this->ride = $ride;
        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['rideId' => $this->getRide()->getId()]);
    }
}
