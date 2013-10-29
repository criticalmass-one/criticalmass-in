<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use Caldera\CriticalmassCoreBundle\Utility\MapElement;

class OtherCitiesMapBuilderModule extends BaseMapBuilderModule
{
    public function execute()
    {
        $cities = $this->mapBuilder->doctrine->getRepository('CalderaCriticalmassCoreBundle:City')->findAll();

        foreach ($cities as $city)
        {
            if (!$city->isEqual($this->mapBuilder->ride->getCity()))
            {
                $marker = new MapElement\CityMarkerMapElement($city);

                $this->mapBuilder->elements[$marker->getId()] = $marker->draw();
            }
        }
    }
} 