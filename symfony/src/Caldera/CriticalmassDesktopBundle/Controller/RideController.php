<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Caldera\CriticalmassCoreBundle\Type\RideType;
use Caldera\CriticalmassStatisticBundle\Entity\RideEstimate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideController extends Controller
{
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

        //$estimate = $this->getDoctrine()->getRepository('CalderaCriticalmassStatisticBundle:RideEstimate');

        $estimate = new RideEstimate();
        $form = $this->createFormBuilder($estimate)
            ->setAction($this->generateUrl('caldera_criticalmass_statistic_ride_estimate', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d'))))
            ->add('estimatedParticipants', 'text')
            ->add('estimatedDistance', 'text')
            ->add('estimatedDuration', 'text')
            ->getForm();

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:show.html.twig', array('city' => $city, 'ride' => $ride, 'estimateForm' => $form->createView()));
    }

    public function estimaterideAction(Request $request, $rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        return new Response();
        //return $this->redirect($this->generateUrl('caldera_criticalmass_desktop_ride_show', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d'))));
    }

    public function editAction(Request $request, $citySlug, $rideDate)
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

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:edit.html.twig', array('ride' => $ride, 'city' => $city, 'form' => $form->createView(), 'hasErrors' => $hasErrors));
    }
}
