<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class CommentImageUploader
{
	private $container;
	private $securityContext;

	private $commentImage;

	private $imageFile;

	public function __construct(SecurityContext $securityContext, ContainerInterface $container)
	{
		$this->securityContext = $securityContext;
		$this->container = $container;
	}

	public function setCommentImage(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;

		return $this;
	}

	public function getCommentImage()
	{
		return $this->commentImage;
	}

	public function setImageFile(\Symfony\Component\HttpFoundation\File\UploadedFile $imageFile)
	{
		$this->imageFile = $imageFile;

		return $this;
	}

	public function processUpload()
	{
		$this->commentImage->setUser($this->securityContext->getToken()->getUser());
		$this->commentImage->setCreationDateTime(new \DateTime());
		$this->commentImage->setName(md5(mt_rand()).'.'.$this->imageFile->guessExtension());

		$this->imageFile->move($this->container->getParameter('commentimage.upload_filepath'), $this->commentImage->getName());

		return $this;
	}
}