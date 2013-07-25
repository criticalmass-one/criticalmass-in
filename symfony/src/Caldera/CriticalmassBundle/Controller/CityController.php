<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CityController extends Controller
{
	public function loadcitiesAction()
	{
		$request = $this->getRequest()->request;

		$cityResults = array();

		if (($latitude = $request->get('latitude')) && ($longitude = $request->get('longitude')))
		{
			$cityResults = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findNearestedByLocation($latitude, $longitude);
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
			$cityResults[$key]['ride'] = $this->get('caldera_criticalmass_ride_repository')->findOneBy(array('city' => $cityResults[$key]['city']->getId()), array('date' => 'desc'));

			if ($latitude && $longitude)
			{
				$cityResults[$key]['distance'] = $this->get('caldera_criticalmass_distancecalculator')->calculateDistanceFromCoordToCoord($cityResults[$key]['city']->getLatitude(), $latitude, $cityResults[$key]['city']->getLongitude(), $longitude);
			}
			else
			{
				$cityResults[$key]['distance'] = null;
			}

			$cityResults[$key]['mainSlug'] = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneByCity($cityResults[$key]['city']);
		}

		return $this->render('CalderaCriticalmassBundle:Rightsidebar:choosecity.html.twig', array('cityResults' => $cityResults));
	}
}
