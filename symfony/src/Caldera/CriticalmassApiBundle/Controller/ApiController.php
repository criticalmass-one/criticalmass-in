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

        // Berechnung der angeforderten Positionsdaten anstossen
        /*$lmp->calculatePositions();
        $lmp->additionalElements();*/

        // neue Antwort zusammensetzen und als JSON klassifizieren
        $response = new Response();
        $response->setContent(json_encode($lmp->draw()));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
