<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\StandardCityType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CityType;
use Caldera\CriticalmassCoreBundle\Utility\CitySlugGenerator\CitySlugGenerator;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends AbstractController
{
    public function listAction()
    {
        $cities = $this->getCityRepository()->findCities();

        return $this->render('CalderaCriticalmassSiteBundle:City:list.html.twig', array('cities' => $cities));
    }

    public function listRidesAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $rides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render('CalderaCriticalmassSiteBundle:City:rideList.html.twig', [
            'city' => $city,
            'rides' => $rides
        ]);

    }

    public function showAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        if (!$city->getEnabled()) {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "' . $citySlug . '" finden :(');
        }

        $currentRide = $this->getRideRepository()->findCurrentRideForCity($city);

        return $this->render('CalderaCriticalmassSiteBundle:City:show.html.twig', [
            'city' => $city,
            'currentRide' => $currentRide,
            'dateTime' => new \DateTime()
        ]);
    }

    public function addAction(Request $request)
    {
        $city = new City();

        $form = $this->createForm(new CityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_add')));

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $csg = new CitySlugGenerator($city);
            $citySlug = $csg->execute();
            $city->addSlug($citySlug);

            $em->persist($citySlug);
            $em->persist($city);
            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(new CityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_edit', array('citySlug' => $city->getMainSlugString()))));
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassSiteBundle:City:edit.html.twig', array('city' => null, 'form' => $form->createView(), 'hasErrors' => $hasErrors));
    }

    public function editAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $form = $this->createForm(new StandardCityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_edit', array('citySlug' => $city->getMainSlugString()))));

        $archiveCity = clone $city;
        $archiveCity->setArchiveUser($this->getUser());
        $archiveCity->setArchiveParent($city);

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->persist($archiveCity);
            $em->flush();

            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassSiteBundle:City:edit.html.twig', array('city' => $city, 'form' => $form->createView(), 'hasErrors' => $hasErrors));
    }

    public function liveAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);
        
        return $this->render('CalderaCriticalmassDesktopBundle:City:live.html.twig', array('city' => $city));
    }

}
