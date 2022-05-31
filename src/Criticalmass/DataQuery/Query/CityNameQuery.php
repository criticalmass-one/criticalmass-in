<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation\QueryAnnotation as DataQuery;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="name")
 */
class CityNameQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /**
     * @Constraints\NotNull()
     */
    protected string $name;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="name")
     */
    public function setName(string $name): CityNameQuery
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
        return new \Elastica\Query\Term(['city' => $this->name]);
    }
}
