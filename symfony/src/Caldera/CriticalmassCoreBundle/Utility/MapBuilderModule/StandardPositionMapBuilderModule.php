<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use \Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain\PositionFilterChain;
use \Caldera\CriticalmassCoreBundle\Utility\MapElement;

class StandardPositionMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $psf = new PositionFilterChain();

        $psf->setDoctrine($this->mapBuilder->doctrine);
        $psf->setRide($this->mapBuilder->ride);
        $psf->execute();

        $this->mapBuilder->positionArray->merge($psf->getPositionArray());

        $counter = 0;

        foreach ($psf->getPositionArray()->getPositions() as $position)
        {
            $circle = new MapElement\CircleMapElement($position, 100);

            $this->mapBuilder->elements['position-'.$counter] = $circle->draw();
            ++$counter;
        }
    }
} 