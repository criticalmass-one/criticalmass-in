<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Parameter;

use App\Criticalmass\DataQuery\Annotation\ParameterAnnotation as DataQuery;
use Elastica\Query;
use Symfony\Component\Validator\Constraints as Constraints;

class OrderDistanceParameter extends AbstractParameter
{
    /**
     * @Constraints\NotNull()
     * @Constraints\Type("float")
     * @var float $latitude
     */
    protected $latitude;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("float")
     * @var float $longitude
     */
    protected $longitude;

    /**
     * @Constraints\NotNull()
     * @Constraints\Type("string")
     * @Constraints\Choice(choices = {"ASC", "DESC"})
     * @var string $direction
     */
    protected $direction;

    /**
     * @DataQuery\RequiredParameter(parameterName="centerLatitude")
     */
    public function setLatitude(float $latitude): OrderDistanceParameter
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredParameter(parameterName="centerLongitude")
     */
    public function setLongitude(float $longitude): OrderDistanceParameter
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @DataQuery\RequiredParameter(parameterName="distanceOrderDirection")
     */
    public function setDirection(string $direction): OrderDistanceParameter
    {
        $this->direction = strtoupper($direction);

        return $this;
    }

    public function addToElasticQuery(Query $query): Query
    {
        $query->addSort([
            '_geo_distance' => [
                'pin' => [
                    $this->longitude,
                    $this->latitude,
                ],
                'order' => $this->direction,
                'unit' => 'km',
            ]
        ]);

        return $query;
    }
}
