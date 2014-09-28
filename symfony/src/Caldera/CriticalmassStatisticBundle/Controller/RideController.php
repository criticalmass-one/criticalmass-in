<?php

namespace Caldera\CriticalmassStatisticBundle\Controller;

use Caldera\CriticalmassCoreBundle\Entity\Track;
use Caldera\CriticalmassCoreBundle\Utility\GpxWriter\GpxWriter;
use Caldera\CriticalmassStatisticBundle\Entity\RideEstimate;
use Caldera\CriticalmassStatisticBundle\Utility\RideEstimateCalculator\RideEstimateCalculator;
use Caldera\CriticalmassStatisticBundle\Utility\RideGuesser\RideGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RideController extends Controller
{
    public function estimateformAction(Request $request, $rideId)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);

        $estimate = new RideEstimate();

        $form = $this->createFormBuilder($estimate)
            ->setAction($this->generateUrl('caldera_criticalmass_statistic_ride_estimate', array('citySlug' => $ride->getCity()->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d'))))
            ->add('estimatedParticipants', 'text')
            ->add('estimatedDistance', 'text')
            ->add('estimatedDuration', 'text')
            ->getForm();

        return $this->render('CalderaCriticalmassStatisticBundle:Ride:estimateform.html.twig', array('form' => $form->createView()));
    }

    public function estimateAction(Request $request, $citySlug, $rideDate)
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

        $estimate = new RideEstimate();

        $form = $this->createFormBuilder($estimate)
            ->setAction($this->generateUrl('caldera_criticalmass_statistic_ride_estimate', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $ride->getDateTime()->format('Y-m-d'))))
            ->add('estimatedParticipants', 'text')
            ->add('estimatedDistance', 'text')
            ->add('estimatedDuration', 'text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $estimate->setRide($ride);
            $estimate->setUser($this->getUser());

            // TODO: This is simply bullshit. Really, we should try to rely on locales here. The validator of our entity accepts also dots or commas
            // but before pushing that shit into our database we need to replace all commas with a dot

            if ($estimate->getEstimatedDistance())
            {
                $estimate->setEstimatedDistance(str_replace(',', '.', $estimate->getEstimatedDistance()));
            }

            if ($estimate->getEstimatedDuration())
            {
                $estimate->setEstimatedDuration(str_replace(',', '.', $estimate->getEstimatedDuration()));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($estimate);
            $em->flush();

            $this->get('caldera.criticalmassstatistic.rideestimate')->calculateEstimates($ride);
        }
        else
        {
            return $this->render('CalderaCriticalmassStatisticBundle:Ride:estimatefailure.html.twig', array('form' => $form->createView(), 'ride' => $ride));
        }

        return $this->redirect($this->generateUrl('caldera_criticalmass_desktop_ride_show', array('citySlug' => $citySlug, 'rideDate' => $ride->getDateTime()->format('Y-m-d'))));
    }
}
