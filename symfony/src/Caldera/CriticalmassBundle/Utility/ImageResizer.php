<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class ImageResizer
{
	private $container;

	private $commentImage;
	private $image;

	public function __construct(\appDevDebugProjectContainer $container)
	{
		$this->container = $container;
	}

	public function loadImage(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;

		$this->image = imagecreatefromjpeg($this->commentImage->getPath());

		return $this;
	}

	public function resize()
	{
		$this->resizeLongSideToLength($this->container->getParameter('commentimage.upload_longside'));

		return $this;
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
		$resizedImage = imagecreatetruecolor($width, $height);

		imagecopyresized($resizedImage, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));

		$this->image = $resizedImage;

		return $this;
	}

	public function save()
	{
		imagejpeg($this->image,
							$this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getId().'-'.imagesx($this->image).'x'.imagesy($this->image).'.jpeg',
							$this->container->getParameter('commentimage.upload_quality'));
	}

	public function getResizedPath()
	{
		return $this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getId().'-'.imagesx($this->image).'x'.imagesy($this->image).'.jpeg';
	}

	public function __destruct()
	{
		imagedestroy($this->image);
	}
}
