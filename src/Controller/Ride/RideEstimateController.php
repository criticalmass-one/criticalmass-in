<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Event\RideEstimate\RideEstimateDeletedEvent;
use App\Event\RideEstimate\RideEstimateUpdatedEvent;
use App\Controller\AbstractController;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Form\Type\RideEstimateEditType;
use App\Form\Type\RideEstimateType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        ?UserInterface $user = null
    ): Response {
        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($user)
            ->setRide($ride);

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate')
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isSubmitted() && $estimateForm->isValid()) {
            $manager = $this->managerRegistry->getManager();
            $manager->persist($estimateForm->getData());
            $manager->flush();

            $eventDispatcher->dispatch(new RideEstimateCreatedEvent($rideEstimate), RideEstimateCreatedEvent::NAME);
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
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        ?UserInterface $user = null
    ): Response {
        $rideEstimate = new RideEstimate();
        $rideEstimate
            ->setUser($this->getUser())
            ->setRide($ride);

        $estimateForm = $this->createForm(RideEstimateType::class, $rideEstimate, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_addestimate_anonymous')
        ]);

        $estimateForm->handleRequest($request);

        if ($estimateForm->isSubmitted() && $estimateForm->isValid()) {
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

    #[IsGranted('edit', 'rideEstimate')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/editestimate/{id}',
        name: 'caldera_criticalmass_ride_editestimate',
        priority: 60
    )]
    public function editAction(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] RideEstimate $rideEstimate,
        Ride $ride,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
    ): Response {
        $form = $this->createForm(RideEstimateEditType::class, $rideEstimate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            $eventDispatcher->dispatch(new RideEstimateUpdatedEvent($rideEstimate), RideEstimateUpdatedEvent::NAME);

            return $this->redirect($objectRouter->generate($ride));
        }

        return $this->render('RideEstimate/edit.html.twig', [
            'form' => $form->createView(),
            'ride' => $ride,
            'rideEstimate' => $rideEstimate,
        ]);
    }

    #[IsGranted('delete', 'rideEstimate')]
    #[Route(
        '/{citySlug}/{rideIdentifier}/deleteestimate/{id}',
        name: 'caldera_criticalmass_ride_deleteestimate',
        methods: ['POST'],
        priority: 60
    )]
    public function deleteAction(
        Request $request,
        #[MapEntity(mapping: ['id' => 'id'])] RideEstimate $rideEstimate,
        Ride $ride,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
    ): Response {
        if (!$this->isCsrfTokenValid('rideestimate_delete_' . $rideEstimate->getId(), $request->request->get('_token'))) {
            return $this->redirect($objectRouter->generate($ride));
        }

        $eventDispatcher->dispatch(new RideEstimateDeletedEvent($rideEstimate), RideEstimateDeletedEvent::NAME);

        $manager = $this->managerRegistry->getManager();
        $manager->remove($rideEstimate);
        $manager->flush();

        return $this->redirect($objectRouter->generate($ride));
    }
}
