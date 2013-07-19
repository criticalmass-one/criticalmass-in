<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class ImageResizer
{
	private $commentImage;

	public function __construct(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;
		$this->image = imagecreatefromjpeg($this->commentImage->getPath());
	}

	public function resizeLongSideToLength($length)
	{
		
	}

	public function resizeTo($width, $height)
	{
		
	}

	public function __destruct()
	{
		imagedestroy($this->image);
	}
}
