<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Event\RideEstimate\RideEstimateUpdatedEvent;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\User;
use App\Form\Type\RideEstimateType;
use App\Repository\RideEstimateRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RideEstimateController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/addestimate',
        name: 'caldera_criticalmass_ride_addestimate',
        priority: 60
    )]
    public function addestimateAction(
        Request $request,
        Ride $ride,
        RideEstimateRepository $rideEstimateRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        ?UserInterface $user = null
    ): Response {
        $isUpdate = false;
        $rideEstimate = null;

        if ($user instanceof User) {
            $rideEstimate = $rideEstimateRepository->findByUserAndRide($user, $ride);
        }

        if ($rideEstimate) {
            $isUpdate = true;
        } else {
            $rideEstimate = new RideEstimate();
            $rideEstimate
                ->setUser($user)
                ->setRide($ride);
        }

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate')
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isSubmitted() && $estimateForm->isValid()) {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($estimateForm->getData());
            $manager->flush();

            if ($isUpdate) {
                $eventDispatcher->dispatch(new RideEstimateUpdatedEvent($rideEstimate), RideEstimateUpdatedEvent::NAME);
            } else {
                $eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimate), RideEstimateCreatedEvent::NAME);
            }
        }

        return $this->redirect($objectRouter->generate($ride));
    }

    #[Route(
        '/{citySlug}/{rideIdentifier}/anonymousestimate',
        name: 'caldera_criticalmass_ride_addestimate_anonymous',
        priority: 60
    )]
    public function anonymousestimateAction(
        Request $request,
        Ride $ride,
        RideEstimateRepository $rideEstimateRepository,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        ?UserInterface $user = null
    ): Response {
        $isUpdate = false;
        $rideEstimate = null;
        $currentUser = $this->getUser();

        if ($currentUser instanceof User) {
            $rideEstimate = $rideEstimateRepository->findByUserAndRide($currentUser, $ride);
        }

        if ($rideEstimate) {
            $isUpdate = true;
        } else {
            $rideEstimate = new RideEstimate();
            $rideEstimate
                ->setUser($currentUser)
                ->setRide($ride);
        }

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate_anonymous')
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isSubmitted() && $estimateForm->isValid()) {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($estimateForm->getData());
            $manager->flush();

            if ($isUpdate) {
                $eventDispatcher->dispatch(new RideEstimateUpdatedEvent($rideEstimate), RideEstimateUpdatedEvent::NAME);
            } else {
                $eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimate), RideEstimateCreatedEvent::NAME);
            }

            return $this->redirect($objectRouter->generate($ride));
        }

        return $this->render('RideEstimate/anonymous.html.twig', [
            'estimateForm' => $estimateForm->createView(),
            'ride' => $ride,
        ]);
    }
}
