<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
                $slug = $ride2->getCity()->getMainSlug()->getSlug();

                $resultArray[$slug] = array(
                    'id' => $ride2->getId(),
                    'slug' => $slug,
                    'dateTime' => $ride2->getDateTime()->format('F d, Y H:i:s'),
                    'hasLocation' => $ride2->getHasLocation(),
                    'title' => $ride2->getTitle(),
                    'description' => $ride2->getDescription());

                if ($ride2->getHasLocation())
                {
                    $resultArray[$slug]['location'] = $ride2->getLocation();
                    $resultArray[$slug]['latitude'] = $ride2->getLatitude();
                    $resultArray[$slug]['longitude'] = $ride2->getLongitude();
                }
            }
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'rides' => $resultArray
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function writecommentAction(Request $request)
    {
        $request = $request->request;

        $comment = new Entity\Comment();

        $comment->setUser($this->getUser());
        $comment->setRide($this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestForCitySlug($request->get('citySlug')));

        $comment->setDateTime(new \DateTime());
        $comment->setLatitude($request->get('latitude'));
        $comment->setLongitude($request->get('longitude'));
        $comment->setMessage($request->get('message'));

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($comment);
        $manager->flush();

        return new Response($comment->getId());
    }
}
