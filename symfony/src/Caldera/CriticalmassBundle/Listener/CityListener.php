<?php

namespace Caldera\CriticalmassBundle\Listener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent as FilterControllerEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;

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

		$citySlug = $event->getRequest()->get("citySlug");

		if (isset($citySlug))
		{
			$city = $city = $this->doctrine->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

			$controller->getRequest()->getSession()->set('currentCitySlug', $city->getMainSlug()->getSlug());
			$controller->getRequest()->getSession()->set('city', $city);

			if ($controller->getUser())
			{
				$user = $controller->getUser();
				$user->setCurrentCity($city);

				$controller->get('fos_user.user_manager')->updateUser($user);
			}
		}
		else
		{
			
		}
	}
}