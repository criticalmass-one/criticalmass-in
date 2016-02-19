<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParticipationController extends AbstractController
{
    public function rideparticipationAction(Request $request, $citySlug, $rideDate, $status)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(), $ride);

        return $this->redirectToRoute($ride);
    }
}
