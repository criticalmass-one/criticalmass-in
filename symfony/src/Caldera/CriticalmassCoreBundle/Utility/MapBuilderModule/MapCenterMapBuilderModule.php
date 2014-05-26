<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;


class MapCenterMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $ride = $this->mapBuilder->ride;

        if ($ride->getHasLocation() && !$ride->isRideRolling())
        {
            $this->mapBuilder->response["mapCenter"] = array(
                "latitude" => $this->mapBuilder->ride->getLatitude(),
                "longitude" => $this->mapBuilder->ride->getLongitude()
            );
        }
        else
        {
            $this->mapBuilder->response["mapCenter"] = array(
                "latitude" => $this->getMapCenterLatitude(),
                "longitude" => $this->getMapCenterLongitude()
            );
        }
    }

    public function getMapCenterLatitude()
    {
        if ($this->mapBuilder->positionArray->countPositions() > 0)
        {
            return $this->calculateMapCenter("getLatitude");

        }
        else
        {
            return $this->mapBuilder->ride->getLatitude();
        }
    }

    public function getMapCenterLongitude()
    {
        if ($this->mapBuilder->positionArray->countPositions() > 0)
        {
            return $this->calculateMapCenter("getLongitude");
        }
        else
        {
            return $this->mapBuilder->ride->getLongitude();
        }
    }

    public function calculateMapCenter($coordinateFunction)
    {
        $min = null;
        $max = null;

        foreach ($this->mapBuilder->positionArray->getPositions() as $position)
        {
            if (!isset($min) && !isset($max) && !$position->getUser()->getIsPermanent())
            {
                $min = $position->$coordinateFunction();
                $max = $position->$coordinateFunction();
            }
            elseif ($min > $position->$coordinateFunction() && !$position->getUser()->getIsPermanent())
            {
                $min = $position->$coordinateFunction();
            }
            elseif ($max < $position->$coordinateFunction() && !$position->getUser()->getIsPermanent())
            {
                $max = $position->$coordinateFunction();
            }
        }

        return $min + ($max - $min) / 2.0;
    }
} 