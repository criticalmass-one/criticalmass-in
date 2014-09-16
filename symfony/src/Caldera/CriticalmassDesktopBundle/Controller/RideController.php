<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Entity\RideEstimate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideController extends Controller
{
    public function showcurrentAction($citySlug)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestForCitySlug($citySlug);

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:showcurrent.html.twig', array('ride' => $ride));
    }

    public function showAction($citySlug, $rideDate)
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
            ->setAction($this->generateUrl('caldera_criticalmass_desktop_ride_estimate', array('rideId' => $ride->getId())))
            ->add('estimatedParticipants', 'text')
            ->add('estimatedDistance', 'text')
            ->add('estimatedDuration', 'text')
            ->add('Schätzen', 'submit')
            ->getForm();

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:show.html.twig', array('city' => $city, 'ride' => $ride, 'estimateForm' => $form->createView()));
    }

    public function estimaterideAction(Request $request, $rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        $estimate = new RideEstimate();
        $form = $this->createFormBuilder($estimate)
            ->setAction($this->generateUrl('caldera_criticalmass_desktop_ride_estimate', array('rideId' => $ride->getId())))
            ->add('estimatedParticipants', 'text')
            ->add('estimatedDistance', 'text')
            ->add('estimatedDuration', 'text')
            ->add('Schätzen', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $estimate->setRide($ride);
            $estimate->setUser($this->getUser());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($estimate);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_desktop_ride_show', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d'))));
    }

    public function proposeAction($citySlug)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestForCitySlug($citySlug);

        $form = $this->createFormBuilder($ride)
            ->add('title', 'text')
            ->add('description', 'text')
            ->add('date', 'date')
            ->add('time', 'time')
            ->add('location', 'text')
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
            ->add('facebook', 'text')
            ->add('twitter', 'text')
            ->add('website', 'text')
            ->add('hasLocation', 'checkbox')
            ->add('hasTime', 'checkbox')
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($this->getRequest());

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:propose.html.twig', array('ride' => $ride, 'form' => $form->createView()));
    }
}
