<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation\QueryAnnotation as DataQuery;
use App\Entity\Region;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="region")
 */
class RegionQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("App\Entity\Region")
     * @var Region $region
     */
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
        return new \Elastica\Query\Term(['region' => $this->region->getName()]);
    }
}
