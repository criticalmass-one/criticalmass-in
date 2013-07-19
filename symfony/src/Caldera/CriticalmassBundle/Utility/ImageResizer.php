<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class ImageResizer
{
	private $commentImage;
	private $image;

	private $newWidth;
	private $newHeight;

	public function __construct(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;
		$this->image = imagecreatefromjpeg($this->commentImage->getPath());
	}

	public function resizeLongSideToLength($length)
	{
		list($width, $height, $type, $attr) = getimagesize($this->commentImage->getPath());

		if ($width > $height)
		{
			$longSide = $width;
		}
		else
		{
			$longSide = $height;
		}

		$resizeFactor = $length / $longSide;

		$this->resizeTo($width * $resizeFactor, $height * $resizeFactor);
	}

	public function resizeTo($width, $height)
	{
		$this->newWidth = $width;
		$this->newHeight = $height;

		imagescale($this->image, $width, $height, IMG_BILINEAR_FIXED);
		imagejpeg($this->image, '/var/www/criticalmass.in/symfony/web/uploads/commentimages/'.$this->commentImage->getId().'-'.$width.'x'.$height.'.jpeg');
	}

	public function getResizedPath()
	{
		return 'http://www.criticalmass.in/uploads/commentimages/'.$this->commentImage->getId().'-'.$this->newWidth.'x'.$this->newHeight.'.jpeg';
	}

	public function __destruct()
	{
		imagedestroy($this->image);
	}
}
