<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Annotation as DataQuery;
use Elastica\Query\AbstractQuery;

class BoundingBoxQuery implements ElasticQueryInterface
{
    /**
     * @var float $northLatitude
     */
    protected $northLatitude;

    /**
     * @var float $southLatitude
     */
    protected $southLatitude;

    /**
     * @var float $eastLongitude
     */
    protected $eastLongitude;

    /**
     * @var float $westLongitude
     */
    protected $westLongitude;
    
    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbNorthLatitude")
     */
    public function setNorthLatitude(float $northLatitude): BoundingBoxQuery
    {
        $this->northLatitude = $northLatitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbSouthLatitude")
     */
    public function setSouthLatitude(float $southLatitude): BoundingBoxQuery
    {
        $this->southLatitude = $southLatitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbEastLongitude")
     */
    public function setEastLongitude(float $eastLongitude): BoundingBoxQuery
    {
        $this->eastLongitude = $eastLongitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredQueryParameter(parameterName="bbWestLongitude")
     */
    public function setWestLongitude(float $westLongitude): BoundingBoxQuery
    {
        $this->westLongitude = $westLongitude;

        return $this;
    }


    public function createElasticQuery(): AbstractQuery
    {
        $geoQuery = new \Elastica\Query\GeoBoundingBox('pin',
            [
                [$this->northLatitude, $this->westLongitude],
                [$this->southLatitude, $this->eastLongitude],
            ]);

        return $geoQuery;
    }
}
