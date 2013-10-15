<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

/**
 * Dieser Controller stellt eine API-Schnittstelle fuer die asynchrone Kommuni-
 * kation mit dem Client bereit. Primaer wird die JavaScript-Implementierung
 * auf dem Client diese Daten anfordern, obwohl theoretisch auch andere Websei-
 * ten Informationen der API abfragen koennten. Fuer einige der Methoden ist
 * eine Anmeldung notwendig.
 */
class ApiController extends Controller
{
	/**
	 * Diese Methode ist die wahrscheinlich wichtigste im gesamten Projekt. Ueber
	 * diese Schnittstelle kann der Client die Daten der aktuellen Kartendarstel-
	 * lung einer ausgewaehlten Stadt anfordern. Die Berechnung und Zusammenset-
	 * zung der Daten geschieht im LiveMapBuilder, der einzelne Aufgaben wiederum
	 * an untergeordnete Klassen und Systeme delegiert.
	 *
	 * Die Auswertung der Daten uebernimmt der Client.
	 *
	 * @param String $citySlug: Kurzbezeichnung der Stadt, deren Daten geladen
	 * werden sollen
	 *
	 * @return String: JSON-Zeichenkette mit allen notwendigen Informationen
	 */
	public function mapdataAction($citySlug)
	{
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

  		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC'));


		$lmp = new Utility\MapBuilder\LiveMapBuilder(
			$ride,
			$this->getDoctrine()
		);

		// Berechnung der angeforderten Positionsdaten anstossen
		$lmp->calculateMainPositions();
		$lmp->calculateAdditionalPositions();

		// neue Antwort zusammensetzen und als JSON klassifizieren
		$response = new Response();
		$response->setContent(json_encode($lmp->draw()));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Gibt den Breiten- und Laengengrad des Treffpunktes der aktuellen Tour der
	 * als Parameter uebermittelten Stadt zurueck. Die Koordinaten werden nur an-
	 * gezeigt, wenn die Daten schon veroeffentlicht worden sind.
	 *
	 * @param String $citySlug: Kurzbezeichnung der Stadt
	 *
	 * @return String: JSON-Antwort mit den angefragten Koordinaten
	 */
	public function ridelocationAction($citySlug)
	{
		// City-Entitaet anhand des Kurznamens aufschluesseln
		$city = $city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

		// aktuelle Tour laden
		$ride = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC'));

		// Antwort zusammenstellen
		$response = new Response();

		// wurden die Daten schon veroeffentlicht?
		if ($ride->getHasLocation())
		{
			// JSON-Array zusammenstellen
			$response->setContent(json_encode(array(
				'latitude' => $ride->getLatitude(),
				'longitude' => $ride->getLongitude()
			)));
		}

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Gibt den geografischen Mittelpunkt der angefragten Stadt zurueck.
	 *
	 * @param String $citySlug: Kurzbezeichnung der Stadt
	 *
	 * @return String: JSON-Array mit dem Mittelpunkt der Stadt
	 */
	public function citylocationAction($citySlug)
	{
		// Stadt herauslesen
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

		// Antwort zusammenstellen
		$response = new Response();

		$response->setContent(json_encode(array(
			'latitude' => $city->getLatitude(),
			'longitude' => $city->getLongitude()
		)));

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Bei der Auswertung von Formularen wird eventuell nicht der Kurzname der
	 * Stadt, sondern nur deren ID als Wert uebertragen. In diesem Falle kann
	 * diese Methode verwendet werden, die ansonsten identisch zu
	 * citylocationAction() ist.
	 *
	 * @param String $citySlug: Kurzbezeichnung der Stadt
	 *
	 * @return String: JSON-Array mit dem Mittelpunkt der Stadt
	 */
	public function citylocationbyidAction($cityId)
	{
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findOneById($cityId);

		$response = new Response();

		$response->setContent(json_encode(array(
			'latitude' => $city->getLatitude(),
			'longitude' => $city->getLongitude()
		)));

		return $response;
	}

	/**
	 * In dieser Methode werden die Positionsdaten des Clients verarbeitet. Da die
	 * Positionsdaten als JSON-Anfrage in diese Methode gereicht werden, koennen 
	 * sie nicht als Parameter ausgelesen werden, sondern muessen aus der Query
	 * des Requests geladen werden.
	 *
	 * Anschliessend werden die Daten in eine Position-Entitaet ueberfuehrt und
	 * abgespeichert.
	 *
	 * @return Integer: ID der Entitaet zu Kontrollzwecken
	 */
	public function trackpositionAction()
	{
		// Query oeffnen
		$query = $this->getRequest()->query;

		// vom Benutzer aktuell ausgewaehlte Stadt laden
		$city = $this->getUser()->getCurrentCity();

		// Positions-Entitaet bereitstellen
		$position = new Entity\Position();

		// Daten zuweisen
		$position->setUser($this->getUser());
		$position->setRide($this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC')));

		$position->setLatitude($query->get("latitude") ? $query->get("latitude") : 0.0);
		$position->setLongitude($query->get("longitude") ? $query->get("longitude") : 0.0);
		$position->setAccuracy($query->get("accuracy") ? $query->get("accuracy") : 0.0);
		$position->setAltitude($query->get("altitude") ? $query->get("altitude") : 0.0);
		$position->setAltitudeAccuracy($query->get("altitudeaccuracy") ? $query->get("altitudeaccuracy") : 0.0);
		$position->setHeading($query->get("heading") ? $query->get("heading") : 0.0);
		$position->setSpeed($query->get("speed") ? $query->get("speed") : 0.0);
		$position->setTimestamp($query->get("timestamp") ? $query->request->get("timestamp") : 0);
		$position->setCreationDateTime(new \DateTime());

		// Entitaet ueber den Manager abspeichern
		$manager = $this->getDoctrine()->getManager();
		$manager->persist($position);
		$manager->flush();

		return new Response($position->getId());
	}

	/**
	 * Diese Methode verarbeitet die Angabe des Intervalls, in das der Client GPS-
	 * Daten senden soll. Das Intervall wird in der Datenbank nicht-fluechtig ge-
	 * speichert.
	 *
	 * @return Integer: Neue Intervall-Angabe zu Kontrollzwecken
	 */
	public function refreshgpsintervalAction()
	{
		$this->getUser()->setGPSInterval($this->getRequest()->query->get('interval'));
		$this->container->get('fos_user.user_manager')->updateUser($this->getUser(), true);

		return new Response($this->getRequest()->query->get('interval'));
	}

	/**
	 * In dieser Methode wird gespeichert, ob der Benutzer GPS-Daten senden moech-
	 * te oder nicht. Die Methode nimmt eine AJAX-Anfrage an, die auf dem Client
	 * beim Umlegen des jeweiligen Schalters ausgeloest wird.
	 *
	 * @return Integer: Kontrollangabe, ob Daten gesendet werden sollen oder nicht
	 */
	public function refreshgpsstatusAction()
	{
		$this->getUser()->setSendGPSInformation($this->getRequest()->query->get('status'));
		$this->container->get('fos_user.user_manager')->updateUser($this->getUser(), true);

		return new Response($this->getRequest()->query->get('status'));
	}

	/**
	 * Gibt das Intervall zurueck, in dem der Benutzer GPS-Daten senden moechte.
	 *
	 * @return Integer: Intervall in Sekunden
	 */
	public function getintervalAction()
	{
		$response = new Response();

		$response->setContent(json_encode(array(
			'interval' => ($this->getUser() != null ? $this->getUser()->getGpsInterval() * 1000 : 0)
		)));

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Gibt an, ob der momentan angemeldete Benutzer GPS-Daten senden moechte oder
	 * diese Funktion in seinem Client deaktiviert hat.
	 *
	 * @return Integer: Boolescher Wert des GPS-Status
	 */
	public function getstatusAction()
	{
		$response = new Response();
		$response->setContent(json_encode(array(
			'status' => ($this->getUser() != null ? $this->getUser()->getSendGPSInformation() : 0)
		)));

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	/**
	 * Mit dieser versteckten Methode kann der so genannte God-Mode umgeschaltet
	 * werden. Wenn dieser Modus eingeschaltet wird, aendert sich die Berechnung
	 * der Positinsdaten, so dass nur noch die Daten des Administrators zur Be
	 * rechnung herangezogen werden. Das ist einerseits fuer Testzwecke nuetzlich,
	 * kann aber auch waehrend einer Tour eingeschaltet werden, wenn die Berech-
	 * nung der Position des Teilnehmerfeldes offenbar fehlerhaft ist oder fal-
	 * sche Daten anzeigt.
	 *
	 * @param String $citySlug: Kurzbezeichnung der Stadt, deren Tour gesichert
	 * werden soll
	 * @param Integer $status: Boolescher Wert
	 */
	public function godmodeAction($citySlug, $status)
	{
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug)->getCity();
		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC'));

		// Status aktualisieren
		$ride->setGodMode($status);

		// und abspeichern
		$manager = $this->getDoctrine()->getManager();
		$manager->persist($ride);
		$manager->flush();

		// kurze Rueckmeldung anzeigen
		return new Response("Godmode fuer ".$city->getCity()." ist: ".$status);
	}

    public function listcitiesAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findAll();

        $citiesResult = array();

        foreach ($cities as $city)
        {
            $cityResultArray = array(
                'id' => $city->getId(),
                'city' => $city->getCity(),
                'title' => $city->getTitle(),
                'description' => $city->getDescription()
            );

            $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC'));

            if ($ride)
            {
                $cityResultArray['center'] = array('latitude' => $ride->getLatitude(), 'longitude' => $ride->getLongitude());
            }
            else
            {
                $cityResultArray['center'] = array('latitude' => $city->getLatitude(), 'longitude' => $city->getLongitude());
            }

            $citiesResult['city-'.$city->getId()] = $cityResultArray;
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'cities' => $citiesResult
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
