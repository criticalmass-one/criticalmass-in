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

    protected function findNearCities(City $city)
    {
        $finder = $this->container->get('fos_elastica.finder.criticalmass.city');

        $archivedFilter = new \Elastica\Filter\Term(['isArchived' => false]);
	$enabledFilter = new \Elastica\Filter\Term(['isEnabled' => true]);
	$selfFilter = new \Elastica\Filter\BoolNot(new \Elastica\Filter\Term(['id' => $city->getId()]));

        $geoFilter = new \Elastica\Filter\GeoDistance(
            'pin',
            [
                'lat' => $city->getLatitude(),
                'lon' => $city->getLongitude()
            ],
            '50km'
        );

        $filter = new \Elastica\Filter\BoolAnd([$archivedFilter, $geoFilter, $enabledFilter, $selfFilter]);

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $filter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(15);
        $query->setSort(
            [
                '_geo_distance' =>
                [
                    'pin' =>
                    [
                        $city->getLatitude(),
                        $city->getLongitude()
                    ],
                'order' => 'desc',
                'unit' => 'km'
                ]
            ]
        );

        $results = $finder->find($query);

        return $results;
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

        $nearCities = $this->findNearCities($city);

        $currentRide = $this->getRideRepository()->findCurrentRideForCity($city);

        $dateTime = new \DateTime();
        $dateTime->setTimezone(new \DateTimeZone($city->getTimezone()));

        return $this->render('CalderaCriticalmassSiteBundle:City:show.html.twig', [
            'city' => $city,
            'currentRide' => $currentRide,
            'dateTime' => $dateTime,
            'nearCities' => $nearCities
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
