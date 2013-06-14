<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityController extends Controller
{
	public function loadcitiesAction()
	{
		$query = $this->getRequest()->query;

		$cityResults = array();

		if ($latitude = $query->get('latitude') && $longitude = $query->get('longitude'))
		{
			$cityResults = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findNearestedByLocation($query->get('latitude'), $query->get('longitude'));
		}
		else
		{
			$tmpResults = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findAll(array(), array('order' => 'asc'));

			foreach ($tmpResults as $result)
			{
				$cityResults[$result->getId()]['city'] = $result;
			}	
		}

		foreach ($cityResults as $key => $result)
		{
			$cityResults[$key]['ride'] = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city_id' => $cityResults[$key]['city']->getId()));

			if ($latitude && $longitude)
			{
				$cityResults[$key]['distance'] = $this->get('caldera_criticalmass_citydistancecalculator')->calculateDistanceFromCoordToCoord($cityResults[$key]['city']->getLatitude(), $latitude, $cityResults[$key]['city']->getLongitude(), $longitude);
			}
			else
			{
				$cityResults[$key]['distance'] = 0.0;
			}

			$cityResults[$key]['mainSlug'] = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneByCity($cityResults[$key]['city']);
		}

		return $this->render('CalderaCriticalmassBundle:Rightsidebar:choosecity.html.twig', array('cityResults' => $cityResults));
	}
}
