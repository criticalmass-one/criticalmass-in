<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function choosecityAction()
	public function choosecityAction($latitude, $longitude)
	{
		$cities = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findNearestedByLocation($latitude, $longitude);

		return $this->render('CalderaCriticalmassBundle:Default:choosecity.html.twig', array('cities' => $cities));
	}

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
			// Darstellung an das Template weiterreichen
			return $this->render('CalderaCriticalmassBundle:Default:index.html.twig', array('city' => $city));
		}
	}
}
