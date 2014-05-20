<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

class RideController extends Controller
{
    public function getcurrentAction()
    {
        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCurrentRides();

        $resultArray = array();

        foreach ($rides as $ride1)
        {
            foreach ($ride1 as $ride2)
            {
                $resultArray[$ride2->getCity()->getMainSlug()->getSlug()] = array(
                    'id' => $ride2->getId(),
                    'slug' => $ride2->getCity()->getMainSlug()->getSlug(),
                    'time' => $ride2->getTime(),
                    'date' => $ride2->getDate(),
                    'location' => $ride2->getLocation(),
                    'latitude' => $ride2->getLatitude(),
                    'longitude' => $ride2->getLongitude()
                );
            }
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'rides' => $resultArray
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
