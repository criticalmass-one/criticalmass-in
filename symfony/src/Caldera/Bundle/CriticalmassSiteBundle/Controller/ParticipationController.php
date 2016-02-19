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

        if (!$participation) {
            $participation = new Participation();
            $participation->setRide($ride);
            $participation->setUser($this->getUser());
        }

        $participation->setGoingYes($status == 'yes');
        $participation->setGoingMaybe($status == 'maybe');
        $participation->setGoingNo($status == 'no');

        $em = $this->getDoctrine()->getManager();
        $em->merge($participation);
        $em->flush();

        $this->recalculateRideParticipations($ride);

        return $this->redirectToRoute($ride);
    }
}
