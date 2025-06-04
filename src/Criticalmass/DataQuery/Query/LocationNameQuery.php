<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation\QueryAnnotation as DataQuery;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="name")
 */
class LocationNameQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    #[Constraints\NotNull]
    protected string $name;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="location")
     */
    public function setName(string $name): LocationNameQuery
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        if ($this->name) {
            return new \Elastica\Query\MatchPhrase('location', $this->name);
        }

        $wildcardQuery = new \Elastica\Query\Wildcard('location', '*');
        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery->addMustNot($wildcardQuery);

        return $boolQuery;
    }
}
