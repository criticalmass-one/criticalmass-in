<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class CommentImageUploader
{
	private $container;

	private $commentImage;

	public function __construct(\appDevDebugProjectContainer $container)
	{
		$this->container = $container;
	}

	public function setCommentImage(Entity\CommentImage $commentImage)
	{
		$this->commentImage = $commentImage;
	}

	public function getCommentImage()
	{
		return $this->commentImage;
	}
}