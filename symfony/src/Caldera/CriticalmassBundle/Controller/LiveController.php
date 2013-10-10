<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Dieser Controller dient lediglich der Anzeige der Live-Darstellung der Tour.
 */
class LiveController extends Controller
{
	/**
	 * Diese Methode dient lediglich der Anzeige der Live-Darstellung der Tour.
	 * Sie laedt die Daten der angefragten Stadt aus der Datenbank und bestueckt
	 * das entsprechende Template.
	 *
	 * @param String citySlug: Kurzbezeichnung der ausgewaehlten Stadt
	 */
	public function showAction($citySlug)
	{
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

		return $this->render('CalderaCriticalmassBundle:Live:show.html.twig', array('city' => $city));
	}
}
