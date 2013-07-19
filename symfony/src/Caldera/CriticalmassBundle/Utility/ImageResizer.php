<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class ImageResizer
{
	private $commentImage;
	private $image;

	private $newWidth;
	private $newHeight;

	public function __construct(\appDevDebugProjectContainer $container)
	{
		$this->container = $container;
	}

	public function loadImage(Entity\CommentImage $commentImage)
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

		$resizedImage = imagecreatetruecolor($width, $height);

		imagecopyresized($resizedImage, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));

		imagejpeg($resizedImage, '/Applications/XAMPP/htdocs/criticalmass/symfony/web/uploads/commentimages/'.$this->commentImage->getId().'-'.$width.'x'.$height.'.jpeg');
	}

	public function getResizedPath()
	{
		return 'http://localhost:8000/uploads/commentimages/'.$this->commentImage->getId().'-'.$this->newWidth.'x'.$this->newHeight.'.jpeg';
	}

	public function __destruct()
	{
		imagedestroy($this->image);
	}
}
