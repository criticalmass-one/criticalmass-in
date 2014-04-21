<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
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
class ApiController extends Controller implements Trackable
{
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
