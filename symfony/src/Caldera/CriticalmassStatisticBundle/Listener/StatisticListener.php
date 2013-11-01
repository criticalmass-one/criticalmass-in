<?php

namespace Caldera\CriticalmassStatisticBundle\Listener;

use Caldera\CriticalmassStatisticBundle\Entity\StatisticVisit;
use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent as FilterControllerEvent;

class StatisticListener
{
	protected $doctrine;

	public function __construct(Doctrine $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	public function onKernelController(FilterControllerEvent $event)
	{

		$controllers = $event->getController();
		$controller = $controllers[0];

        if ($controller instanceof Trackable)
        {
            $visit = new StatisticVisit();
            $visit->setHost($_SERVER['HTTP_HOST']);
            $visit->setRemoteAddr($_SERVER['REMOTE_ADDR']);
            $visit->setAgent($_SERVER['HTTP_USER_AGENT']);
            $visit->setDateTime(new \DateTime());
            $visit->setRemoteHost(gethostbyaddr($_SERVER['REMOTE_ADDR']));
            $visit->setHost("");
            $visit->setReferer(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
            $visit->setHost($_SERVER['SERVER_NAME']);
            $visit->setQuery($_SERVER['REQUEST_URI']);
            $visit->setEnvironment('dev');

            if ($controller->getRequest()->getSession()->has('city'))
            {
                $visit->setCity($controller->getRequest()->getSession()->get('city'));
            }

            if ($controller->getUser())
            {
                $visit->setUser($controller->getUser());
                $controller->get('fos_user.user_manager')->updateUser($controller->getUser());
            }

            $manager = $this->doctrine->getManager();
            $manager->persist($visit);
            $manager->flush();
        }
/**
		// auf die citySlug-Eigenschaft zugreifen
		$citySlug = $event->getRequest()->get("citySlug");

		// wurde eine Bezeichnung einer Stadt uebergeben?
		if (isset($citySlug))
		{
			// CitySlug-Instanz laden
			$city = $this->doctrine->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

			// Eigenschaften in der Session persistieren
			$controller->getRequest()->getSession()->set('currentCitySlug', $city->getMainSlug()->getSlug());
			$controller->getRequest()->getSession()->set('city', $city);

			// handelt es sich um einen angemeldeten Benutzer?
			if ($controller->getUser())
			{
				// dann zusaetzlich Aenderungen in die Datenbank schreiben
				$user = $controller->getUser();
				$user->setCurrentCity($city);

				$controller->get('fos_user.user_manager')->updateUser($user);
			}
		}*/
	}
}