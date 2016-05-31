<?php

namespace Caldera\Bundle\CriticalmassPhotoBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            64
        );

        return $this->render(
            'CalderaCriticalmassPhotoBundle:Default:index.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }
}
