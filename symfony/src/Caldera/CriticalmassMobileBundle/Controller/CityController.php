<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Der CityController enthaelt lediglich eine einzige Methode, die zur Be-
 * stueckung der Liste der Staedte im aufklappbaren Menue auf der rechten Sei-
 * te der Web-App dient.
 */
class CityController extends Controller
{
	/**
	 * Diese Methode stellt eine Liste der in der Datenbank vorhandenen Staedte
	 * zusammen und gibt ein entsprechend ausgestattetes Template zurueck. Die
	 * Staedte werden nach ihrer Entfernung zur Position des Benutzers sortiert,
	 * sofern dessen Geolocation-Daten uebermittelt werden. Da die Ergebnisse zu-
	 * saetzlich mit der berechneten Entfernung attributiert werden muessen, ist
	 * der Quellcode etwas umfangreicher. Das Template wird schliesslich einfach
	 * in den Inhalt der rechten Sidebar eingefuegt.
	 *
	 * @return String: Ausgestattetes HTML-Template
	 */
	public function loadcitiesAction()
	{
		// Request in einer Variable zur Verfuegung stellen
		$request = $this->getRequest()->request;

		// Array fur die Anzeige der Ergebnisse vorbereiten
		$cityResults = array();

		// stehen die Geolocation-Daten zur vErfuegung
		if (($latitude = $request->get('latitude')) && ($longitude = $request->get('longitude')))
		{
			// Orte nach ihrer Entfernung sortieren
			$cityResults = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findNearestedByLocation($latitude, $longitude);
		}
		else
		{
			// ansonsten nach dem Alphabet vorgehen
			$tmpResults = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findAll(array(), array('order' => 'asc'));

			foreach ($tmpResults as $result)
			{
				$cityResults[$result->getId()]['city'] = $result;
			}	
		}

		// falls moeglich wird hier noch die Entfernung zum Benutzer angegeben
		foreach ($cityResults as $key => $result)
		{
			$cityResults[$key]['ride'] = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city' => $cityResults[$key]['city']->getId()), array('date' => 'desc'));

			if ($latitude && $longitude)
			{
				$cityResults[$key]['distance'] = $this->get('caldera_criticalmass_distancecalculator')->calculateDistanceFromCoordToCoord($cityResults[$key]['city']->getLatitude(), $latitude, $cityResults[$key]['city']->getLongitude(), $longitude);
			}
			else
			{
				$cityResults[$key]['distance'] = null;
			}
		}

		// Template rendern und zurueckgeben
		return $this->render('CalderaCriticalmassMobileBundle:Rightsidebar:choosecity.html.twig', array('cityResults' => $cityResults));
	}
}
