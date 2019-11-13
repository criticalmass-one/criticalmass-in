<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use App\Entity\Region;
use Elastica\Query\AbstractQuery;

class RegionQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /** @var Region $region */
    protected $region;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="region")
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

    public function createElasticQuery(): AbstractQuery
    {
        return \Elastica\Query\Term(['city.region' => $this->region->getId()]);
    }
}
