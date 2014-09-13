<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:show.html.twig', array('city' => $city, 'ride' => $ride));
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
