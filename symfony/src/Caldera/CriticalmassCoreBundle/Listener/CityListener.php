<?php

namespace Caldera\CriticalmassCoreBundle\Listener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent as FilterControllerEvent;

/**
 * Dieser Event-Listener wird jeweils vor jedem Controller-Aufruf alarmiert und
 * ueberprueft, ob der Benutzer in der jetzigen Aktion seine Stadt gewechselt
 * hat. Falls die Kurzbezeichnung einer Zeichenkette uebergeben wurde, wird die
 * neue Stadt in der Datenbank und in der Session des Benutzers abgespeichert.
 */
class CityListener
{
	/**
	 * Doctrine-Instanz zum Zugriff auf die CitySlugs.
	 */
	protected $doctrine;

	/**
	 * Erzeugt den EventListener. Es wird automatisch eine Doctrine-Instanz vom
	 * Symfony-Service erzeugt.
	 *
	 * @param Doctrine $doctrine: Doctrine-Instanz
	 */
	public function __construct(Doctrine $doctrine)
	{
		$this->doctrine = $doctrine;
	}

	/**
	 * Diese Methode beinhaltet die eigentliche Logik des Event-Listeners.
	 *
	 * @param FilterControllerEvent $event: Event-Instanz zum Zugriff auf Control-
	 * ler-Eigenschaften
	 */
	public function onKernelController(FilterControllerEvent $event)
	{
		// Controller laden
		$controllers = $event->getController();
		$controller = $controllers[0];

		// auf die citySlug-Eigenschaft zugreifen
		$citySlug = $event->getRequest()->get("citySlug");

		// wurde eine Bezeichnung einer Stadt uebergeben?
		if (isset($citySlug))
		{
			// CitySlug-Instanz laden
			$citySlug2 = $this->doctrine->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

            if (!empty($citySlug2))
            {
                $city = $citySlug2->getCity();

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
            }
		}
	}
}