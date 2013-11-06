<?php

namespace Caldera\CriticalmassGalleryBundle\Utility\MapElement;

use Caldera\CriticalmassCoreBundle\Utility\MapElement\BaseMapElement;
use Caldera\CriticalmassGalleryBundle\Entity\Image;

class ImageMapElement extends BaseMapElement
{
    protected $image;

	public function __construct(Image $image)
	{
        $this->image = $image;
	}

	public function getId()
	{
		return 'image-'.$this->image->getExifLatitude().'-'.$this->image->getExifLongitude();
	}

	public function draw()
	{
		return array(
			'id' => $this->getId(),
			'type' => 'image',
			'centerPosition' => array('latitude' => $this->image->getExifLatitude(), 'longitude' => $this->image->getExifLongitude()),
            'imagePath' => 'http://www.criticalmass.local/image/display/'.$this->image->getId()
			);
	}
}