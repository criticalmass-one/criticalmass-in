<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Entity\Region;

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
}
