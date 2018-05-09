<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Ride;

use Criticalmass\Component\Participation\Calculator\RideParticipationCalculatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ParticipationController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function rideparticipationAction(RideParticipationCalculatorInterface $rideParticipationCalculator, UserInterface $user, Ride $ride, string $status): Response
    {
        $participation = $this->getParticipationRepository()->findParticipationForUserAndRide($this->getUser(), $ride);

        if (!$participation) {
            $participation = new Participation();
            $participation
                ->setRide($ride)
                ->setUser($user);
        }

        $participation
            ->setGoingYes($status === 'yes')
            ->setGoingMaybe($status === 'maybe')
            ->setGoingNo($status === 'no');

        $this->recalculateRideParticipations($rideParticipationCalculator, $ride);

        $em = $this->getDoctrine()->getManager();
        $em->persist($participation);
        $em->flush();

        return $this->redirectToObject($ride);
    }

    protected function recalculateRideParticipations(RideParticipationCalculatorInterface $rideParticipationCalculator, Ride $ride): void
    {
        $rideParticipationCalculator
            ->setRide($ride)
            ->calculate();
    }
}
