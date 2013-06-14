<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityController extends Controller
{
	public function loadcitiesAction($latitude, $longitude)
	{
		$cityResults = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findNearestedByLocation($latitude, $longitude);

		foreach ($cityResults as $key => $result)
		{
			$cityResults[$key]['ride'] = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city_id' => $cityResults[$key]['city']->getId()));
			$cityResults[$key]['distance'] = $this->get('caldera_criticalmass_citydistancecalculator')->calculateDistanceFromCoordToCoord($cityResults[$key]['city']->getLatitude(), $latitude, $cityResults[$key]['city']->getLongitude(), $longitude);
			$cityResults[$key]['mainSlug'] = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneByCity($cityResults[$key]['city']);
		}

		return $this->render('CalderaCriticalmassBundle:Rightsidebar:choosecity.html.twig', array('cityResults' => $cityResults));
	}
}
