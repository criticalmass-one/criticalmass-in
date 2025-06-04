<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="pin", propertyType="string")
 */
class RadiusQuery extends AbstractQuery implements ElasticQueryInterface
{
    /**
     * @var float $centerLatitude
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -90, max: 90)]
    protected $centerLatitude;

    /**
     * @var float $centerLongitude
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -180, max: 180)]
    protected $centerLongitude;

    /**
     * @var float $radius
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: 0, max: 50000)]
    protected $radius;

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="centerLatitude")
     */
    public function setCenterLatitude(float $centerLatitude): RadiusQuery
    {
        $this->centerLatitude = $centerLatitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="centerLongitude")
     */
    public function setCenterLongitude(float $centerLongitude): RadiusQuery
    {
        $this->centerLongitude = $centerLongitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="radius")
     */
    public function setRadius(float $radius): RadiusQuery
    {
        $this->radius = $radius;

        return $this;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $kmDistance = sprintf('%dkm', $this->radius);

        $geoQuery = new \Elastica\Query\GeoDistance('pin', [
            'lat' => $this->centerLatitude,
            'lon' => $this->centerLongitude,
        ],
            $kmDistance
        );

        return $geoQuery;
    }
}
