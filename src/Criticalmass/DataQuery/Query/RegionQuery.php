<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Region;
use Elastica\Query\AbstractQuery;

class RegionQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /** @var Region $region */
    protected $region;

    public function __construct(Region $region)
    {
        $this->region = $region;
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
