<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Attribute\QueryAttribute as DataQuery;
use Elastica\Query\BoolQuery;
use Elastica\Query\Wildcard;
use Symfony\Component\Validator\Constraints as Constraints;

#[DataQuery\RequiredEntityProperty(propertyName: 'pin', propertyType: 'string')]
class HasCoordinatesQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    #[Constraints\IsTrue]
    protected bool $coordsRequired = false;

    #[DataQuery\RequiredQueryParameter(parameterName: 'has_coords')]
    public function setCoordsRequired(bool $hasCoords): HasCoordinatesQuery
    {
        $this->coordsRequired = $hasCoords;
        return $this;
    }

    public function getCoordsRequired(): bool
    {
        return $this->coordsRequired;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $boolQuery = new BoolQuery();
        $boolQuery->addMust(new Wildcard('latitude', '*'));
        $boolQuery->addMust(new Wildcard('longitude', '*'));

        return $boolQuery;
    }
}
