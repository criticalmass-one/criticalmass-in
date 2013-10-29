<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

class ZoomFactorMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $minX = null;
        $maxX = null;
        $minY = null;
        $maxY = null;

        // alle Positionsdaten durchlaufen
        foreach ($this->mapBuilder->positionArray->getPositions() as $position)
        {
            // handelt es sich um das erste Datum?
            if (!isset($minX) && !isset($maxX) && !isset($minY) && !isset($maxY))
            {
                $minX = $position->getLatitude();
                $maxX = $position->getLatitude();
                $minY = $position->getLongitude();
                $maxY = $position->getLongitude();
            }
            else
            {
                // neues horizontales Minimum?
                if ($minX > $position->getLatitude())
                {
                    $minX = $position->getLatitude();
                }

                // neues horizontales Maximum?
                if ($maxX < $position->getLatitude())
                {
                    $maxX = $position->getLatitude();
                }

                // neues vertikales Minimum?
                if ($minY > $position->getLongitude())
                {
                    $minY = $position->getLongitude();
                }

                // neues vertikales Maximum?
                if ($maxY < $position->getLongitude())
                {
                    $maxY = $position->getLongitude();
                }
            }
        }

        // Ausdehnung bestimmen?
        $distanceX = $maxX - $minX;
        $distanceY = $maxY - $minY;

        // Distanz der Flaeche berechnen
        $distance = ($distanceX > $distanceY ? $distanceX : $distanceY) + 0.001;

        // Zoom-Faktor fuer Google Maps bestimmen
        $zoomFactor = floor(log(960 * 360 / $distance / 256)) + 1;

        $this->mapBuilder->response['zoomFactor'] = $zoomFactor;
    }
} 