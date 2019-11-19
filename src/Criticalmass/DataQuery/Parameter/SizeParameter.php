<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use Elastica\Query;

class SizeParameter implements ParameterInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("int")
     * @Constraints\Range(min="0", max="500")
     * @var int $size
     */
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
