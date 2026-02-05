<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Elastica\Query\BoolQuery;
use Elastica\Query\Wildcard;
use Symfony\Component\Validator\Constraints as Constraints;

class LocationNameQuery
{
    #[Constraints\IsTrue]
    protected bool $coordsRequired = false;

    public function setCoordsRequired(bool $hasCoords): LocationNameQuery
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
