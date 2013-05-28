<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassBundle\Utility as Utility;
use Caldera\CriticalmassBundle\Entity as Entity;

class MapController extends Controller
{
	public function mapcenterAction()
	{
		$mph = new Utility\MapPositionHandler(new Entity\Ride());

		$response = new Response();
		$response->setContent(json_encode(array(
			'mapcenter' => array(
				'latitude' => $mph->getMapCenterLatitude(),
				'longitude' => $mph->getMapCenterLongitude()
			),
			'zoom' => 10,
			'positions' => array(
				'city1' => array(
					'latitude' => 53.57033623530256,
					'longitude' => 9.719623122674422
				),
				'city2' => array(
					'latitude' => 53.57033623130256,
					'longitude' => 9.719623128674422
				)
			)
		)));

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}
