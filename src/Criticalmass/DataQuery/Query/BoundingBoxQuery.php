<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use MalteHuebner\DataQueryBundle\Annotation\QueryAnnotation as DataQuery;
use MalteHuebner\DataQueryBundle\Query\AbstractQuery;
use MalteHuebner\DataQueryBundle\Query\ElasticQueryInterface;
use MalteHuebner\DataQueryBundle\Validator\Constraint\BoundingBoxValues;
use Symfony\Component\Validator\Constraints as Constraints;

/**
 * @DataQuery\RequiredEntityProperty(propertyName="pin", propertyType="string")
 * @BoundingBoxValues()
 */
class BoundingBoxQuery extends AbstractQuery implements ElasticQueryInterface
{
    /**
     * @var float $northLatitude
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -90, max: 90)]
    protected $northLatitude;

    /**
     * @var float $southLatitude
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -90, max: 90)]
    protected $southLatitude;

    /**
     * @var float $eastLongitude
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -180, max: 180)]
    protected $eastLongitude;

    /**
     * @var float $westLongitude
     */
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -180, max: 180)]
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

    public function hasNorthLatitude(): bool
    {
        return $this->northLatitude !== null;
    }

    public function hasSouthLatitude(): bool
    {
        return $this->southLatitude !== null;
    }

    public function hasEastLongitude(): bool
    {
        return $this->eastLongitude !== null;
    }

    public function hasWestLongitude(): bool
    {
        return $this->westLongitude !== null;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $geoQuery = new \Elastica\Query\GeoBoundingBox('pin',
            [
                [$this->westLongitude, $this->northLatitude,],
                [$this->eastLongitude, $this->southLatitude,],
            ]);

        return $geoQuery;
    }
}
