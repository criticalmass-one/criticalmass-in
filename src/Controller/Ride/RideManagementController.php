<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Form\Type\RideDisableType;
use App\Form\Type\RideSocialPreviewType;
use Carbon\Carbon;
use App\Form\Type\RideType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RideManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/add-ride', name: 'caldera_criticalmass_ride_add', priority: 70)]
    public function addAction(Request $request, EntityManagerInterface $entityManager, City $city, ObjectRouterInterface $objectRouter, ?UserInterface $user = null): Response
    {
        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setUser($user)
            ->setLatitude($city->getLatitude())
            ->setLongitude($city->getLongitude())
        ;

        $form = $this->createForm(RideType::class, $ride, [
            'action' => $objectRouter->generate($city,'caldera_criticalmass_ride_add'),
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $ride, $entityManager, $objectRouter, $city, $form, $user);
        } else {
            return $this->addGetAction($request, $ride, $entityManager, $objectRouter, $city, $form, $user);
        }
    }

    protected function addGetAction(Request $request, Ride $ride, EntityManagerInterface $entityManager, ObjectRouterInterface $objectRouter, City $city, FormInterface $form, ?UserInterface $user = null): Response
    {
        return $this->render('RideManagement/edit.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => Carbon::now(),
        ]);
    }

    protected function addPostAction(
        Request $request,
        Ride $ride,
        EntityManagerInterface $entityManager,
        ObjectRouterInterface $objectRouter,
        City $city,
        FormInterface $form,
        ?UserInterface $user = null
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride = $form->getData();

            $entityManager->persist($ride);
            $entityManager->flush();

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(RideType::class, $ride, [
                'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_edit'),
            ]);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($ride));
        }

        return $this->render('RideManagement/edit.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => Carbon::now(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/edit', name: 'caldera_criticalmass_ride_edit', priority: 70)]
    public function editAction(Request $request, Ride $ride, ObjectRouterInterface $objectRouter, ?UserInterface $user = null): Response
    {
        $form = $this->createForm(RideType::class, $ride, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_edit'),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $ride, $ride->getCity(), $form, $objectRouter, $user);
        } else {
            return $this->editGetAction($request, $ride, $ride->getCity(), $form, $objectRouter, $user);
        }
    }

    protected function editGetAction(
        Request $request,
        Ride $ride,
        City $city,
        FormInterface $form,
        ObjectRouterInterface $objectRouter,
        ?UserInterface $user = null
    ): Response {
        return $this->render('RideManagement/edit.html.twig', [
            'ride' => $ride,
            'city' => $city,
            'form' => $form->createView(),
            'dateTime' => Carbon::now(),
        ]);
    }

    protected function editPostAction(
        Request $request,
        Ride $ride,
        City $city,
        FormInterface $form,
        ObjectRouterInterface $objectRouter,
        ?UserInterface $user = null
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride
                ->setUpdatedAt(Carbon::now())
                ->setUser($user);

            if ($ride->isEnabled()) {
                $ride->setDisabledReason(null);
            }

            $this->managerRegistry->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($ride));
        }

        return $this->render('RideManagement/edit.html.twig', [
            'ride' => $ride,
            'city' => $city,
            'form' => $form->createView(),
            'dateTime' => Carbon::now(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/socialpreview', name: 'caldera_criticalmass_ride_socialpreview', priority: 70)]
    public function socialPreviewAction(
        EntityManagerInterface $entityManager,
        Request $request,
        ObjectRouterInterface $objectRouter,
        Ride $ride,
        ?UserInterface $user = null
    ): Response {
        $form = $this->createForm(RideSocialPreviewType::class, $ride, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_socialpreview'),
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->socialPreviewPostAction($entityManager, $request, $ride, $form, $user);
        } else {
            return $this->socialPreviewGetAction($entityManager, $request, $ride, $form, $user);
        }
    }

    protected function socialPreviewGetAction(
        EntityManagerInterface $entityManager,
        Request $request,
        Ride $ride,
        Form $form,
        ?UserInterface $user = null
    ): Response {
        return $this->render('RideManagement/social_preview.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function socialPreviewPostAction(
        EntityManagerInterface $entityManager,
        Request $request,
        Ride $ride,
        Form $form,
        ?UserInterface $user = null
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride
                ->setUpdatedAt(Carbon::now())
                ->setUser($user);

            $entityManager->flush();

            $request->getSession()->getFlashBag()->add('success', 'Änderungen gespeichert!');
        }

        return $this->socialPreviewGetAction($entityManager, $request, $ride, $form, $user);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/disable', name: 'caldera_criticalmass_ride_disable', priority: 70)]
    public function disableAction(Request $request, ManagerRegistry $registry, Ride $ride, ObjectRouterInterface $objectRouter, ?UserInterface $user = null): RedirectResponse
    {
        if (Request::METHOD_POST === $request->getMethod()) {
            $disableForm = $this->createForm(RideDisableType::class, $ride);
            $disableForm->handleRequest($request);

            if ($disableForm->isSubmitted() && $disableForm->isValid()) {
                $ride->setEnabled(false);

                $registry->getManager()->flush();
            }
        }

        return $this->redirect($objectRouter->generate($ride));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/{rideIdentifier}/enable', name: 'caldera_criticalmass_ride_enable', priority: 70)]
    public function enableAction(ManagerRegistry $registry, Ride $ride, ObjectRouterInterface $objectRouter, ?UserInterface $user = null): RedirectResponse
    {
        $ride->setEnabled(true)
            ->setDisabledReason(null)
            ->setDisabledReasonMessage(null);

        $registry->getManager()->flush();

        return $this->redirect($objectRouter->generate($ride));
    }
}
