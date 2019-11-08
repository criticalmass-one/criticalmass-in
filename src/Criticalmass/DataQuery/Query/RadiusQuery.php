<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

class RadiusQuery implements ElasticQueryInterface
{
    /** @var float $centerLatitude */
    protected $centerLatitude;

    /** @var float $centerLongitude */
    protected $centerLongitude;

    /** @var float $radius */
    protected $radius;

    public function __construct(float $centerLatitude, float $centerLongitude, float $radius)
    {
        $this->centerLatitude = $centerLatitude;
        $this->centerLongitude = $centerLongitude;
        $this->radius = $radius;
    }

    public function getCenterLatitude(): float
    {
        return $this->centerLatitude;
    }

    public function getCenterLongitude(): float
    {
        return $this->centerLongitude;
    }

    public function getRadius(): float
    {
        return $this->radius;
    }
}
