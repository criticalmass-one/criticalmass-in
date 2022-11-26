<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use MalteHuebner\DataQueryBundle\Annotation\ParameterAnnotation as DataQuery;
use MalteHuebner\DataQueryBundle\Parameter\AbstractParameter;
use MalteHuebner\DataQueryBundle\Parameter\PropertyTargetingParameterInterface;
use MalteHuebner\DataQueryBundle\Validator\Constraint\Sortable;
use Elastica\Query;
use Symfony\Component\Validator\Constraints as Constraints;

class OrderParameter extends AbstractParameter implements PropertyTargetingParameterInterface
{
    /**
     * @Sortable
     * @var string $propertyName
     */
    #[Constraints\NotNull]
    #[Constraints\Type('string')]
    protected $propertyName;

    /**
     * @var string $direction
     */
    #[Constraints\NotNull]
    #[Constraints\Type('string')]
    #[Constraints\Choice(choices: ['ASC', 'DESC'])]
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
