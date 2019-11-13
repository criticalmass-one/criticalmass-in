<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use Elastica\Query\AbstractQuery;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="pin", propertyType="string")
 */
class RadiusQuery implements ElasticQueryInterface
{
    /** @var float $centerLatitude */
    protected $centerLatitude;

    /** @var float $centerLongitude */
    protected $centerLongitude;

    /** @var float $radius */
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

    public function createElasticQuery(): AbstractQuery
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
