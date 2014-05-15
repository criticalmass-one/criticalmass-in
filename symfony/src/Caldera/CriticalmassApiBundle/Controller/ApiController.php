<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

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
    public function completemapdataAction($citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC'));

        $lmp = new Utility\MapBuilder\LiveMapBuilder(
            $ride,
            $this->getDoctrine()
        );

        $lmp->registerModules();
        $lmp->execute();

        // neue Antwort zusammensetzen und als JSON klassifizieren
        $response = new Response();
        $response->setContent(json_encode($lmp->draw()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Diese Methode verarbeitet die Angabe des Intervalls, in das der Client GPS-
     * Daten senden soll. Das Intervall wird in der Datenbank nicht-fluechtig ge-
     * speichert.
     *
     * @return Integer: Neue Intervall-Angabe zu Kontrollzwecken
     */
    public function gpsintervalAction()
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
    public function gpsstatusAction()
    {
        $this->getUser()->setSendGPSInformation($this->getRequest()->query->get('status'));
        $this->container->get('fos_user.user_manager')->updateUser($this->getUser(), true);

        return new Response($this->getRequest()->query->get('status'));
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
        $position->setRide($this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneBy(array('city' => $city->getId()), array('date' => 'DESC')));

        $position->setLatitude($query->get("latitude") ? $query->get("latitude") : 0.0);
        $position->setLongitude($query->get("longitude") ? $query->get("longitude") : 0.0);
        $position->setAccuracy($query->get("accuracy") ? $query->get("accuracy") : 0.0);
        $position->setAltitude($query->get("altitude") ? $query->get("altitude") : 0.0);
        $position->setAltitudeAccuracy($query->get("altitudeaccuracy") ? $query->get("altitudeaccuracy") : 0.0);
        $position->setHeading($query->get("heading") ? $query->get("heading") : 0.0);
        $position->setSpeed($query->get("speed") ? $query->get("speed") : 0.0);
        $position->setTimestamp($query->get("timestamp") ? $query->get("timestamp") : 0);
        $position->setCreationDateTime(new \DateTime());

        // Entitaet ueber den Manager abspeichern
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($position);
        $manager->flush();

        return new Response($position->getId());
    }

    /**
     * Gibt das Intervall zurueck, in dem der Benutzer GPS-Daten senden moechte.
     *
     * @return Integer: Intervall in Sekunden
     */
    public function getgpsintervalAction()
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
    public function getgpsstatusAction()
    {
        $response = new Response();
        $response->setContent(json_encode(array(
            'status' => ($this->getUser() != null ? $this->getUser()->getSendGPSInformation() : 0)
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
