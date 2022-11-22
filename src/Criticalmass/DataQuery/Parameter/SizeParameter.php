<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use MalteHuebner\DataQueryBundle\Annotation\ParameterAnnotation as DataQuery;
use Elastica\Query;
use MalteHuebner\DataQueryBundle\Parameter\AbstractParameter;
use Symfony\Component\Validator\Constraints as Constraints;

class SizeParameter extends AbstractParameter
{
    /**
     * @var int $size
     */
    #[Constraints\NotNull]
    #[Constraints\Type('int')]
    #[Constraints\Range(min: 1, max: 500)]
    protected $size;

    /**
     * @DataQuery\RequiredParameter(parameterName="size")
     */
    public function setSize(int $size): SizeParameter
    {
        $this->size = $size;

        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->setSize($this->size);
    }
}
