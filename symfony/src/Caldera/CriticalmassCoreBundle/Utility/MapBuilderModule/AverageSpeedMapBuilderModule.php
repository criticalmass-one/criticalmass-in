<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use \Caldera\CriticalmassCoreBundle\Utility as Utility;

class AverageSpeedMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        // liegen ueberhaupt genuegend Positionen zur Berechnung vor?
        if ($this->mapBuilder->positionArray->countPositions() < 2)
        {
            $averageSpeed = 0;
        }
        else
        {
            // Abstand der Positionsdaten bestimmen
            $dc = new Utility\DistanceCalculator();
            $distance = $dc->calculateDistanceFromPositionToPosition(
                $this->mapBuilder->positionArray->getPosition(0),
                $this->mapBuilder->positionArray->getPosition(1));

            // zeitliche Differenz berechnen
            $time = $this->mapBuilder->positionArray->getPosition(0)->getCreationDateTime()->format('U') -
                $this->mapBuilder->positionArray->getPosition(1)->getCreationDateTime()->format('U');

            // Durchschnittsgeschwindigkeit berechnen
            $averageSpeed = $distance / ($time + 0.001);

            // in Kilometer pro Stunde umrechnen
            $averageSpeed *= 3600;
        }

        $this->mapBuilder->response['averageSpeed'] = round($averageSpeed, 2);
    }
} 