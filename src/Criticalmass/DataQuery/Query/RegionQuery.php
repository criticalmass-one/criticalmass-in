<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Entity\Region;

class RegionQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /** @var Region $region */
    protected $region;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="regionSlug")
     */
    public function setRegion(Region $region): RegionQuery
    {
        $this->region = $region;

        return $this;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        return new \Elastica\Query\Term(['region' => $this->region->getId()]);
    }
}
