<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

class DefaultController extends Controller implements Trackable
{
	/**
	 * Ruft ein Template auf, dass per JavaScript die Position des Endgerätes
	 * ausliest und an die nächste Action weiterleitet.
	 */
	public function selectcityAction()
	{
		return $this->render('CalderaCriticalmassMobileBundle:MultiPage:main.html.twig');
	}

	/**
	 * Lädt die angegebene Stadt aus der Datenbank und reicht sie an das Template zur Anzeige weiter.
	 *
	 * @param $city Name der Stadt
	 */
	public function showcityAction($citySlug)
	{
		// aufzurufende Stadt anhand des Slugs ermitteln
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

		// wurde die Stadt überhaupt gefunden?
		if (empty($citySlug))
		{
			// Fehlermeldung werfen
			throw $this->createNotFoundException('This city does not exist');
		}
		else
		{
			// Stadt anhand des übergebenen Parameters laden
			$city = $citySlug->getCity();

			$ride = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC'));

			// Darstellung an das Template weiterreichen
			return $this->render('CalderaCriticalmassMobileBundle:Default:tour.html.twig', array('city' => $city, 'ride' => $ride));
		}
	}

	/**
	 * Diese Methode ruft ein Formular auf, in das der Benutzer seinen persoenli-
	 * chen Pushover-Schluessel eintragen kann. Wenn das Formular abgesendet wur-
	 * de, wird der Key in der User-Entitaet gespeichert.
	 */
	public function pushnotificationsAction()
	{
		$form = $this->createFormBuilder($this->getUser())->add('pushoverkey', 'text')->getForm();

		$form->handleRequest($this->getRequest());

		if ($form->isValid())
		{
			$this->container->get('fos_user.user_manager')->updateUser($this->getUser(), true);
		}

		return $this->render('CalderaCriticalmassMobileBundle:Default:pushnotifications.html.twig', array('form' => $form->createView()));
	}
}
