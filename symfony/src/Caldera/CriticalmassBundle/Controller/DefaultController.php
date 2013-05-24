<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	/**
	 * Zeigt eine Liste der Critical-Mass-Touren in der Umgebung an.
	 *
	 * @param $latitude Breitengrad des Suchpunktes
	 * @param $longitude Längengrad des Suchpunktes
	 */
	public function choosecityAction($latitude, $longitude)
	{
		$cityResults = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findNearestedByLocation($latitude, $longitude);

		return $this->render('CalderaCriticalmassBundle:Default:choosecity.html.twig', array('cityResults' => $cityResults));
	}

	/**
	 * Ruft ein Template auf, dass per JavaScript die Position des Endgerätes
	 * ausliest und an die nächste Action weiterleitet.
	 */
	public function selectcityAction()
	{
		return $this->render('CalderaCriticalmassBundle:Default:selectcity.html.twig');
	}

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
			$ride = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city_id' => $city->getId()));

			// Darstellung an das Template weiterreichen
			return $this->render('CalderaCriticalmassBundle:Default:index.html.twig', array('city' => $city, 'ride' => $ride));
		}
	}
}
