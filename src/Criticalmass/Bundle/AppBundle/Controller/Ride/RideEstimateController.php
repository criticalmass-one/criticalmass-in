<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Controller\Ride;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\RideEstimate;
use Criticalmass\Bundle\AppBundle\Form\Type\RideEstimateType;
use Criticalmass\Component\Statistic\RideEstimate\RideEstimateService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RideEstimateController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addestimateAction(Request $request, UserInterface $user, string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($user)
            ->setRide($ride)
        ;

        $estimateForm = $this->createForm(
            RideEstimateType::class,
            $rideEstimate,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_ride_addestimate',
                    [
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getFormattedDate()
                    ]
                )
            ]
        );

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $this->getManager()->persist($estimateForm->getData());
            $this->getManager()->flush();

            $this->recalculateEstimates($ride);
        }

        return $this->redirectToObject($ride);
    }

    protected function recalculateEstimates(Ride $ride): void
    {
        $estimateService = $this->get(RideEstimateService::class);
        $estimateService->calculateEstimates($ride);
    }
}
