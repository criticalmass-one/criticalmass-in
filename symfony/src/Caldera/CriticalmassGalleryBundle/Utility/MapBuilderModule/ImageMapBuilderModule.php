<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\MapBuilderModule;

use Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule\BaseMapBuilderModule;
use Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain as PositionFilterChain;
use Caldera\CriticalmassGalleryBundle\Utility\MapElement as MapElement;

class ImageMapBuilderModule extends BaseMapBuilderModule
{
	public function execute()
	{
        $images = $this->mapBuilder->doctrine->getRepository('CalderaCriticalmassGalleryBundle:Image')->findAll();

        foreach ($images as $image)
        {
            $marker = new MapElement\ImageMapElement($image);

            $this->mapBuilder->elements[$marker->getId()] = $marker->draw();
        }
	}
}