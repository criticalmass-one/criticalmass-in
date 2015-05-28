<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Ride;
use Caldera\CriticalmassCoreBundle\Entity\SubRide;
use Caldera\CriticalmassCoreBundle\Type\SubRideType;
use Caldera\CriticalmassStatisticBundle\Type\RideEstimateType;
use Caldera\CriticalmassCoreBundle\Type\RideType;
use Caldera\CriticalmassStatisticBundle\Entity\RideEstimate;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubRideController extends Controller
{
    public function addAction(Request $request, $citySlug, $rideDate)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Minimass erstellen zu können.');
        }

        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        $rideDateTime = new \DateTime($rideDate);

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        $subRide = new SubRide();
        $subRide->setDateTime($ride->getDateTime());
        $subRide->setRide($ride);
        $subRide->setUser($this->getUser());

        $form = $this->createForm(new SubRideType(), $subRide, array('action' => $this->generateUrl('caldera_criticalmass_desktop_subride_add', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $rideDate))));

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(new RideType(), $ride, array('action' => $this->generateUrl('caldera_criticalmass_desktop_ride_edit', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $ride->getFormattedDate()))));

            // QND: this is a try to serve an instance of the new created subride to get the marker to the right place
            return $this->render('CalderaCriticalmassDesktopBundle:SubRide:edit.html.twig', array('hasErrors' => $hasErrors, 'subRide' => $subRide, 'form' => $form->createView(), 'city' => $city, 'ride' => $ride));
        }
        elseif ($form->isSubmitted())
        {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:SubRide:edit.html.twig', array('hasErrors' => $hasErrors, 'subRide' => null, 'form' => $form->createView(), 'city' => $city, 'ride' => $ride));
    }

    public function editAction(Request $request, $subRideId)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Tour bearbeiten zu können.');
        }

        $subRide = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:SubRide')->find($subRideId);

        if (!$subRide)
        {
            throw new NotFoundHttpException('Wir haben leider keine Mini-Mass mit der ID '.$subRideId.' gefunden.');
        }

        $archiveRide = clone $subRide;
        $archiveRide->setArchiveUser($this->getUser());
        $archiveRide->setArchiveParent($subRide);
        $archiveRide->setIsArchived(true);
        $archiveRide->setArchiveDateTime(new \DateTime());

        $form = $this->createForm(new SubRideType(), $subRide, array('action' => $this->generateUrl('caldera_criticalmass_desktop_subride_edit', array('subRideId' => $subRideId))));

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

        return $this->render('CalderaCriticalmassDesktopBundle:SubRide:edit.html.twig', array('ride' => $subRide->getRide(), 'subRide' => $subRide, 'form' => $form->createView(), 'hasErrors' => $hasErrors, 'dateTime' => new \DateTime()));
    }
    
    public function preparecopyAction(Request $request, $citySlug, $rideDate)
    {
        if (!$this->getUser()) {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Minimass erstellen zu können.');
        }

        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj) {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit ' . $citySlug . ' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        $rideDateTime = new \DateTime($rideDate);

        $newRide = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$newRide)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in ' . $city->getCity() . ' am ' . $rideDateTime->format('d. m. Y') . ' gefunden.');
        }
        
        $oldRide = $newRide->getPreviousRide();

        return $this->render('CalderaCriticalmassDesktopBundle:SubRide:copy.html.twig', array('oldRide' => $oldRide, 'newRide' => $newRide));
    }
}
