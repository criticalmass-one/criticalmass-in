<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimelapseController extends AbstractController
{
    public function showAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        return $this->render('CalderaCriticalmassSiteBundle:Timelapse:show.html.twig', array('ride' => $ride));
    }
}
