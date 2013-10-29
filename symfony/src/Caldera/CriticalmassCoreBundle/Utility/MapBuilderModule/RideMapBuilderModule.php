<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use Caldera\CriticalmassCoreBundle\Utility\MapElement\RideMarkerMapElement;

class RideMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $marker = new RideMarkerMapElement($this->mapBuilder->ride);

        $this->mapBuilder->elements[$marker->getId()] = $marker->draw();
    }
} 