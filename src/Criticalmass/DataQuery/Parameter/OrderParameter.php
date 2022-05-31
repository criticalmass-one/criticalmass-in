<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use App\Criticalmass\DataQuery\Annotation\ParameterAnnotation as DataQuery;
use App\Criticalmass\DataQuery\Validator\Constraint\Sortable;
use Elastica\Query;
use Symfony\Component\Validator\Constraints as Constraints;

class OrderParameter extends AbstractParameter implements PropertyTargetingParameterInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("string")
     * @Sortable
     * @var string $propertyName
     */
    protected $propertyName;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("string")
     * @Constraints\Choice(choices = {"ASC", "DESC"})
     * @var string $direction
     */
    protected $direction;

    /**
     * @DataQuery\RequiredParameter(parameterName="orderBy")
     */
    public function setPropertyName(string $propertyName): OrderParameter
    {
        $this->propertyName = $propertyName;

        return $this;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    /**
     * @DataQuery\RequiredParameter(parameterName="orderDirection")
     */
    public function setDirection(string $direction): OrderParameter
    {
        $this->direction = strtoupper($direction);

        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->addSort([$this->propertyName => ['order' => $this->direction]]);
    }
}
