<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RearrangeController extends Controller
{
    public function rearrangeAction(Request $request, $citySlug, $rideDate)
    {
        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        try {
            $rideDateTime = new \DateTime($rideDate);
        }
        catch (\Exception $e)
        {
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfange. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        return $this->render('CalderaCriticalmassGalleryBundle:Rearrange:rearrange.html.twig', array('city' => $city, 'ride' => $ride, 'dateTime' => new \DateTime()));
    }

    public function loadtrackAction(Request $request, $trackId)
    {
        $track = $this->getDoctrine()->getRepository('CalderaCriticalmassTrackBundle:Track')->find($trackId);

        $gr = new GpxReader();
        $gr->loadTrack($track);

        $json = $gr->generateJsonDateTimeArray(25);

        return new Response($json);
    }

    public function loadphotosAction(Request $request, $rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        $photos = $this->getDoctrine()->getRepository('CalderaCriticalmassGalleryBundle:Photo')->findBy(array('ride' => $ride, 'user' => $this->getUser()), array('dateTime' => 'ASC'));
        
        $json = '[';
        $first = true;
        
        foreach ($photos as $photo)
        {
            if (!$first)
            {
                $json .= ', ';
            }

            $json .= '{ "id": "'.$photo->getId().'", "lat": "'.$photo->getLatitude().'", "lng": "'.$photo->getLongitude().'", "dateTime": "'.$photo->getDateTime()->format('U').'" }';
            
            $first = false;
        }
        
        $json .= ']';
        
        return new Response($json);
    }
}
