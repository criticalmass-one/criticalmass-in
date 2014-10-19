<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassStatisticBundle\Type\RideEstimateType;
use Caldera\CriticalmassCoreBundle\Type\RideType;
use Caldera\CriticalmassStatisticBundle\Entity\RideEstimate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideController extends Controller
{
    public function listAction(Request $request)
    {
        $ridesResult = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findRidesInInterval();

        $rides = array();

        foreach ($ridesResult as $ride)
        {
            $rides[$ride->getFormattedDate()][] = $ride;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:list.html.twig', array('rides' => $rides));
    }

    public function showcurrentAction($citySlug)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestForCitySlug($citySlug);

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:showcurrent.html.twig', array('ride' => $ride));
    }

    public function showAction(Request $request, $citySlug, $rideDate)
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

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:show.html.twig', array('city' => $city, 'ride' => $ride, 'dateTime' => new \DateTime()));
    }

    public function estimaterideAction(Request $request, $rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        return new Response();
        //return $this->redirect($this->generateUrl('caldera_criticalmass_desktop_ride_show', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d'))));
    }

    public function addAction(Request $request, $citySlug)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Tour erstellen zu können.');
        }

        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();
        $ride = new Ride();
        $ride->setCity($city);

        $form = $this->createForm(new RideType(), $ride, array('action' => $this->generateUrl('caldera_criticalmass_desktop_ride_add', array('citySlug' => $city->getMainSlugString()))));

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid() && !$city->hasRideAtMonthDay($ride->getDateTime()))
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(new RideType(), $ride, array('action' => $this->generateUrl('caldera_criticalmass_desktop_ride_edit', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $ride->getFormattedDate()))));
        }
        elseif ($form->isSubmitted())
        {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:edit.html.twig', array('hasErrors' => $hasErrors, 'ride' => null, 'form' => $form->createView(), 'city' => $city, 'dateTime' => new \DateTime()));
    }


    public function editAction(Request $request, $citySlug, $rideDate)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Tour bearbeiten zu können.');
        }

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
            throw new NotFoundHttpException('Mit diesem Datum können wir leider nichts anfangen. Bitte gib ein Datum im Format YYYY-MM-DD an.');
        }

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        $archiveRide = clone $ride;
        $archiveRide->setArchiveUser($this->getUser());
        $archiveRide->setArchiveParent($ride);

        $form = $this->createForm(new RideType(), $ride, array('action' => $this->generateUrl('caldera_criticalmass_desktop_ride_edit', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d')))));

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->persist($archiveRide);
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;
        }
        elseif ($form->isSubmitted())
        {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:edit.html.twig', array('ride' => $ride, 'city' => $city, 'form' => $form->createView(), 'hasErrors' => $hasErrors, 'dateTime' => new \DateTime()));
    }
}
