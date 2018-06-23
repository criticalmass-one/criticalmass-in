<?php declare(strict_types=1);

namespace AppBundle\Controller\Ride;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Ride;
use AppBundle\Entity\RideEstimate;
use AppBundle\Form\Type\RideEstimateType;
use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RideEstimateController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function addestimateAction(
        Request $request,
        UserInterface $user,
        Ride $ride
    ): Response {
        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($user)
            ->setRide($ride);

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $this->generateUrl('caldera_criticalmass_ride_addestimate', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate()
            ])
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $this->getManager()->persist($estimateForm->getData());
            $this->getManager()->flush();

            $this->recalculateEstimates($ride);
        }

        return $this->redirectToObject($ride);
    }

    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function anonymousestimateAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride
    ): Response {
        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($this->getUser())
            ->setRide($ride);

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $this->generateUrl('caldera_criticalmass_ride_addestimate_anonymous', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate()
            ])
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $this->getManager()->persist($estimateForm->getData());
            $this->getManager()->flush();

            $this->recalculateEstimates($ride);

            return $this->redirectToObject($ride);
        }

        return $this->render('AppBundle:RideEstimate:anonymous.html.twig', [
            'estimateForm' => $estimateForm->createView(),
            'ride' => $ride,
        ]);
    }

    protected function recalculateEstimates(Ride $ride): void
    {
        $estimateService = $this->get(RideEstimateService::class);
        $estimateService->calculateEstimates($ride);
    }
}
