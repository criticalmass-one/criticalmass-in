<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	/**
	 * Lädt die angegebene Stadt aus der Datenbank und reicht sie an das Template zur Anzeige weiter.
	 *
	 * @param $city Name der Stadt
	 */
	public function showcityAction($city)
	{
		// Stadt anhand des übergebenen Parameters laden
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findOneByCity($city);

		// wurde die Stadt überhaupt gefunden?
		if (empty($city))
		{
			// Fehlermeldung werfen
			throw $this->createNotFoundException('This city does not exist');
		}
		else
		{
			$ride = $this->get('caldera_criticalmass_ride_repository')->findLatest();

			// Darstellung an das Template weiterreichen
			return $this->render('CalderaCriticalmassBundle:Default:index.html.twig', array('city' => $city, 'ride' => $ride));
		}
	}
}
