<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use App\Entity\Region;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\DoctrineQueryInterface;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="region")
 */
class RegionQuery extends AbstractQuery implements DoctrineQueryInterface, ElasticQueryInterface
{
    /**
     * @var Region $region
     */
    #[Constraints\NotNull]
    #[Constraints\Type(\App\Entity\Region::class)]
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
        $regionQuery = new \Elastica\Query\BoolQuery();
        $regionQuery->addShould(new \Elastica\Query\Term(['region' => $this->region->getName()]));
        $regionQuery->addShould(new \Elastica\Query\Term(['country' => $this->region->getName()]));
        $regionQuery->addShould(new \Elastica\Query\Term(['continent' => $this->region->getName()]));

        return $regionQuery;
    }
}
