<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use Elastica\Query;

class From implements ParameterInterface
{
    /** @var int $from */
    protected $from;

    /**
     * @DataQuery\RequiredParameter(parameterName="from")
     */
    public function setFrom(int $from): From
    {
        $this->from = $from;

        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        return $query->setFrom($this->from);
    }
}
