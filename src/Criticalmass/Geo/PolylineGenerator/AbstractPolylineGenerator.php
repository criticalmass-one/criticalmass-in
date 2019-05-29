<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\PolylineGenerator;

use App\Criticalmass\Geo\PolylineGenerator\PolylineStrategy\PolylineStrategyInterface;

abstract class AbstractPolylineGenerator implements PolylineGeneratorInterface
{
    /** @var PolylineStrategyInterface $polylineStrategy */
    protected $polylineStrategy;

    public function setStrategy(PolylineStrategyInterface $polylineStrategy): PolylineGeneratorInterface
    {
        $this->polylineStrategy = $polylineStrategy;

        return $this;
    }
}
