<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;

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
}
