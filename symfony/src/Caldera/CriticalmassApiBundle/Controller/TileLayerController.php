<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

class TileLayerController extends Controller
{
    public function listtilelayersAction()
    {
        $user = $this->getUser();

        $resultArray = array();

        $tileLayers = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:TileLayer')->findBy(array('active' => true), array('title' => 'ASC'));

        foreach ($tileLayers as $tileLayer)
        {
            $resultArray[$tileLayer->getId()] = array(
                                   'id' => $tileLayer->getId(),
                                   'title' => $tileLayer->getTitle(),
                                   'address' => ($tileLayer->getPlusOnly() && !$user ? '' : $tileLayer->getAddress()),
                                   'attribution' => $tileLayer->getAttribution(),
                                   'standard' => $tileLayer->getStandard());
        }

        // neue Antwort zusammensetzen und als JSON klassifizieren
        $response = new Response();
        $response->setContent(json_encode($resultArray));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
