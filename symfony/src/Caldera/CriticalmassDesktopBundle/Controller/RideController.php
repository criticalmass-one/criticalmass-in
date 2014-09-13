<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RideController extends Controller
{
    public function showcurrentAction($citySlug)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findLatestForCitySlug($citySlug);

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:showcurrent.html.twig', array('ride' => $ride));
    }

    public function showAction($citySlug, $rideDate)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        $rideDateTime = new \DateTime($rideDate);

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        return $this->render('CalderaCriticalmassDesktopBundle:Ride:showcurrent.html.twig', array('city' => $city, 'ride' => $ride));
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
