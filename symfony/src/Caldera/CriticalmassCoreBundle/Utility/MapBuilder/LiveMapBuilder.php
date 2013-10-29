<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilder;

use Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain as PositionFilterChain;
use Caldera\CriticalmassCoreBundle\Utility\MapElement as MapElement;
use Caldera\CriticalmassCoreBundle\Utility\MapBuilder\MapBuilderHelper as MapBuilderHelper;
use Caldera\CriticalmassCoreBundle\Utility as Utility;

class LiveMapBuilder extends BaseMapBuilder
{
    public function registerModules()
    {
        $this->registerModule("MapCenterMapBuilderModule");
        $this->registerModule("StandardPositionMapBuilderModule");
        $this->registerModule("PermanentPositionMapBuilderModule");
        $this->registerModule("AverageSpeedMapBuilderModule");
        $this->registerModule("ZoomFactorMapBuilderModule");
        $this->registerModule("UserOnlineMapBuilderModule");
        $this->registerModule("RideMapBuilderModule");
        $this->registerModule("OtherCitiesMapBuilderModule");
    }






/**
    public function calculatePermanentPositions()
    {
        $psf = new PositionFilterChain\PermanentPositionFilterChain();

        $psf->setDoctrine($this->doctrine);
        $psf->setRide($this->ride);
        $psf->execute();

        $this->positionArray->merge($psf->getPositionArray());

        foreach ($psf->getPositionArray()->getPositions() as $position)
        {
            $marker = new MapElement\PositionMarkerMapElement($position);

            $this->elements[$marker->getId()] = $marker->draw();
        }
    }

    public function additionalElements()
    {/*
        if ($this->positionArray->countPositions() > 1)
        {
            $arrow = new MapElement\ArrowMapElement($positionArray[0], $positionArray[1]);
            $elements[] = $arrow->draw();
        }

        $this->calculatePermanentPositions();
        $marker = new MapElement\RideMarkerMapElement($this->ride);
        $this->elements[] = $marker->draw();
    }*/
}
