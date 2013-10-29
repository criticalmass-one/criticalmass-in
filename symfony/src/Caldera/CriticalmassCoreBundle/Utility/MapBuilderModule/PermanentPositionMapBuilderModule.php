<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain as PositionFilterChain;
use Caldera\CriticalmassCoreBundle\Utility\MapElement as MapElement;


class PermanentPositionMapBuilderModule extends BaseMapBuilderModule
{
	public function execute()
	{
        $psf = new PositionFilterChain\PermanentPositionFilterChain();

        $psf->setDoctrine($this->mapBuilder->doctrine);
        $psf->setRide($this->mapBuilder->ride);
        $psf->execute();

        foreach ($psf->getPositionArray()->getPositions() as $position)
        {
            $marker = new MapElement\PositionMarkerMapElement($position);

            $this->mapBuilder->elements[$marker->getId()] = $marker->draw();
        }
	}
}