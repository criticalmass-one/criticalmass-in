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

	public function setCommentImage(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;

		$this->image = imagecreatefromjpeg($this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getPath());

		return $this;
	}

	public function getCommentImage()
	{
		return $this->commentImage;
	}

	public function resize()
	{
		$this->resizeLongSideToLength($this->container->getParameter('commentimage.upload_longside'));

		return $this;
	}

	public function resizeLongSideToLength($length)
	{
		if (imagesx($this->image) > imagesy($this->image))
		{
			$longSide = imagesx($this->image);
		}
		else
		{
			$longSide = imagesy($this->image);
		}

		$resizeFactor = $length / $longSide;

		$this->resizeTo(imagesx($this->image) * $resizeFactor, imagesy($this->image) * $resizeFactor);
	}

	public function resizeTo($width, $height)
	{
		$resizedImage = imagecreatetruecolor($width, $height);

		imagecopyresized($resizedImage, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));

		$this->image = $resizedImage;

		$this->commentImage->setResizedWidth(imagesx($this->image));
		$this->commentImage->setResizedHeight(imagesy($this->image));
	}

	public function save()
	{
		imagejpeg($this->image,
							$this->container->getParameter('commentimage.upload_filepath').$this->commentImage->getName().'-'.imagesx($this->image).'x'.imagesy($this->image).'.jpeg',
							$this->container->getParameter('commentimage.upload_quality'));

		return $this;
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
