<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class ImageResizer
{
	private $commentImage;

	public function __construct(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;
	}

	public function resizeLongSideToLength($length)
	{
		
	}

	public function resizeTo($width, $height)
	{
		
	}
}