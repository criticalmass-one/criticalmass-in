<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\PolylineGenerator;

/**
 * @deprecated
 */
class PolylineGenerator extends AbstractPolylineGenerator
{
    public function execute(): PolylineGeneratorInterface
    {
        $list = $this->trackReader->slicePublicCoords();

        $polyline = \Polyline::Encode($list);

        $this->polyline = $polyline;

        return $this;
    }
} 
