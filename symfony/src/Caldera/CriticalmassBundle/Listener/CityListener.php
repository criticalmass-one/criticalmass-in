<?php

namespace Caldera\CriticalmassBundle\Listener;

use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent as FilterControllerEvent;
use Caldera\CriticalmassBundle\Controller as Controller;

class CityListener
{
	protected $doctrine;
	protected $container;

	public function __construct(Doctrine $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function onKernelController(FilterControllerEvent $event)
	{
		$controllers = $event->getController();
		$controller = $controllers[0];

		if ($controller instanceof Controller\LiveController)
		{
			$controller->getUser();
		}

		//->container->get('security.context')->getUser();

		/*
		$user = $event->getUser();

		if($user)
		{
			$user->setLastLogin(new \DateTime());
			$user->setNumberOfLogins($user->getNumberOfLogins() + 1);
			$em = $this->doctrine->getEntityManager();
			$em->flush();
		}*/
	}
}