<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Entity\StatisticTrack;
use Caldera\CriticalmassStatisticBundle\Utility\StatisticEntityWriter\StatisticEntityWriter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{
    public function trackAction()
    {
        $track = new StatisticTrack();

        $sew = new StatisticEntityWriter($this, $track);

        $track = $sew->execute();

        $track->setActionType($this->getRequest()->query->get("actionType"));
        $track->setElementName($this->getRequest()->query->get("elementName"));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($track);
        $manager->flush();

        $response = new Response();

        return $response;
    }
}
