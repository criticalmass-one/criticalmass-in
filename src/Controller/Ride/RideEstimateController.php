<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Form\Type\RideEstimateType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RideEstimateController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function addestimateAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter
    ): Response {
        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($user)
            ->setRide($ride);

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate')
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($estimateForm->getData());
            $manager->flush();

            $eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimate), RideEstimateCreatedEvent::NAME);
        }

        return $this->redirect($objectRouter->generate($ride));
    }

    public function anonymousestimateAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter
    ): Response {
        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($this->getUser())
            ->setRide($ride);

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate_anonymous')
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isValid()) {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($estimateForm->getData());
            $manager->flush();

            $eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimate), RideEstimateCreatedEvent::NAME);

            return $this->redirect($objectRouter->generate($ride));
        }

        return $this->render('RideEstimate/anonymous.html.twig', [
            'estimateForm' => $estimateForm->createView(),
            'ride' => $ride,
        ]);
    }
}
