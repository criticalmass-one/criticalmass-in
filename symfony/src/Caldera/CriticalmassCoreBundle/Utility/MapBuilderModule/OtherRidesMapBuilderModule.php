<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use Caldera\CriticalmassCoreBundle\Utility\MapElement;

class OtherRidesMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $rides = $this->mapBuilder->doctrine->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCurrentRides();

        foreach ($rides as $ride1)
        {
            foreach ($ride1 as $ride2)
            {
                if (!$ride2->isEqual($this->mapBuilder->ride))
                {
                    $marker = new MapElement\RideMarkerMapElement($ride2);

                    $this->mapBuilder->elements[$marker->getId()] = $marker->draw();
                }
            }
        }
    }
} 