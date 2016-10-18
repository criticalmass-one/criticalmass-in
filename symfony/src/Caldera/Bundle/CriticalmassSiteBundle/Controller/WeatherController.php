<?php

namespace Caldera\CriticalmassOpenweatherBundle\Controller;

use Caldera\CriticalmassOpenweatherBundle\Utility\Weather\OpenWeatcherQuery;
use Caldera\CriticalmassOpenweatherBundle\Utility\Weather\OpenWeatherReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $dateTime = new \DateTime();
        $startDateTime = new \DateTime('2015-02-05');
        $endDateTime = new \DateTime('2015-02-08');

        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findRidesInInterval($startDateTime, $endDateTime);

        $owq = new OpenWeatcherQuery();

        foreach ($rides as $ride)
        {
            $ride->getDateTime()->format('Y-m-d H:i:s').'<br />';

            $owq->setRide($ride);

            $json = $owq->execute();

            $owr = new OpenWeatherReader();
            $owr->setDate($ride->getDateTime());
            $owr->setJson($json);

            $weather = $owr->createEntity();
            $weather->setRide($ride);

            $em = $this->getDoctrine()->getManager();
            $em->persist($weather);
        }

        $em->flush();
        
        return new Response();
    }
}
