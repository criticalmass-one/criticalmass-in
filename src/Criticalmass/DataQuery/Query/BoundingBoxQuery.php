<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

class BoundingBoxQuery implements QueryInterface
{
    /** @var float $northLatitude */
    protected $northLatitude;

    /** @var float $southLatitude */
    protected $southLatitude;

    /** @var float $eastLongitude */
    protected $eastLongitude;

    /** @var float $westLongitude */
    protected $westLongitude;

    public function __construct(float $northLatitude, float $southLatitude, float $eastLongitude, float $westLongitude)
    {
        $this->northLatitude = $northLatitude;
        $this->southLatitude = $southLatitude;
        $this->eastLongitude = $eastLongitude;
        $this->westLongitude = $westLongitude;
    }

    public function getNorthLatitude(): float
    {
        return $this->northLatitude;
    }

    public function getSouthLatitude(): float
    {
        return $this->southLatitude;
    }

    public function getEastLongitude(): float
    {
        return $this->eastLongitude;
    }

    public function getWestLongitude(): float
    {
        return $this->westLongitude;
    }
}
