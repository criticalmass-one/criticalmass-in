<?php

namespace Caldera\CriticalmassBundle\Utility;

use \Caldera\CriticalmassBundle\Entity as Entity;

class CommentImageUploader
{
	private $container;

	public function __construct(\appDevDebugProjectContainer $container)
	{
		$this->container = $container;
	}

}