<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use MalteHuebner\DataQueryBundle\Attribute\ParameterAttribute as DataQuery;
use Elastica\Query;
use MalteHuebner\DataQueryBundle\Parameter\AbstractParameter;
use Symfony\Component\Validator\Constraints as Constraints;

class FromParameter extends AbstractParameter
{
    #[Constraints\NotNull]
    #[Constraints\Type('int')]
    #[Constraints\Range(min: 0)]
    protected int $from;

    #[DataQuery\RequiredParameter(parameterName: 'from')]
    public function setFrom(int $from): FromParameter
    {
        $this->from = $from;
        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->setFrom($this->from);
    }
}
