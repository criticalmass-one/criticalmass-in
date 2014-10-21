<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Caldera\CriticalmassCoreBundle\Type\CityType;
use Caldera\CriticalmassCoreBundle\Type\StandardCityType;
use \Caldera\CriticalmassCoreBundle\Entity\City as City;
use Caldera\CriticalmassCoreBundle\Utility\CitySlugGenerator\CitySlugGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityController extends Controller
{
    public function listAction()
    {
        $cities = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->findCities();

        return $this->render('CalderaCriticalmassDesktopBundle:City:list.html.twig', array('cities' => $cities));
    }

    public function showAction(Request $request, $citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        if (!$city->getEnabled())
        {
            throw new NotFoundHttpException('Wir konnten keine Stadt unter der Bezeichnung "'.$citySlug.'" finden :(');
        }

        $rides = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findBy(array('city' => $city->getId()), array('dateTime' => 'DESC'));

        if ($city->getCurrentRide())
        {
            array_shift($rides);
            // shift the first ride from the array as the first one is the current and should not be displayed at the recent rides list
        }

        return $this->render('CalderaCriticalmassDesktopBundle:City:show.html.twig', array('city' => $city, 'rides' => $rides, 'dateTime' => new \DateTime()));
    }

    public function addAction(Request $request)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Stadt hinzufügen zu können.');
        }

        $city = new City();

        $form = $this->createForm(new CityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_add')));

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $csg = new CitySlugGenerator($city);
            $citySlug = $csg->execute();
            $city->addSlug($citySlug);

            $em->persist($citySlug);
            $em->persist($city);
            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(new CityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_edit', array('citySlug' => $city->getMainSlugString()))));
        }
        elseif ($form->isSubmitted())
        {
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:City:edit.html.twig', array('city' => null, 'form' => $form->createView(), 'hasErrors' => $hasErrors));
    }

    public function editAction(Request $request, $citySlug)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Stadt bearbeiten zu können.');
        }

        $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

        $form = $this->createForm(new StandardCityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_edit', array('citySlug' => $city->getMainSlugString()))));

        $archiveCity = clone $city;
        $archiveCity->setArchiveUser($this->getUser());
        $archiveCity->setArchiveParent($city);

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->persist($archiveCity);
            $em->flush();

            $hasErrors = false;
        }
        elseif ($form->isSubmitted())
        {
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:City:edit.html.twig', array('city' => $city, 'form' => $form->createView(), 'hasErrors' => $hasErrors));
    }
}
