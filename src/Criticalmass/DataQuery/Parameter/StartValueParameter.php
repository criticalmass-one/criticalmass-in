<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use MalteHuebner\DataQueryBundle\Annotation\ParameterAnnotation as DataQuery;
use Elastica\Query;
use Symfony\Component\Validator\Constraints as Constraints;

class StartValueParameter extends OrderParameter
{
    /**
     * @var $startValue
     */
    #[Constraints\NotNull]
    protected $startValue;

    /**
     * @DataQuery\RequiredParameter(parameterName="startValue")
     */
    public function setStartValue($startValue): StartValueParameter
    {
        $this->startValue = $startValue;

        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        $whereClause = [];

        if ($this->direction === 'ASC') {
            $whereClause['gte'] = $this->startValue;
        } else {
            $whereClause['lte'] = $this->startValue;
        }
        
        $startQuery = new \Elastica\Query\Range($this->propertyName, $whereClause);

        $query->getQuery()->addMust($startQuery);

        return $query;
    }
}
