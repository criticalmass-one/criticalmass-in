<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Form\Type\RideDisableType;
use App\Form\Type\RideSocialPreviewType;
use App\Form\Type\RideType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RideManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function addAction(Request $request, UserInterface $user = null, EntityManagerInterface $entityManager, City $city, ObjectRouterInterface $objectRouter): Response
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
            return $this->addPostAction($request, $user, $ride, $entityManager, $objectRouter, $city, $form);
        } else {
            return $this->addGetAction($request, $user, $ride, $entityManager, $objectRouter, $city, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user = null, Ride $ride, EntityManagerInterface $entityManager, ObjectRouterInterface $objectRouter, City $city, FormInterface $form): Response
    {
        return $this->render('RideManagement/edit.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => new \DateTime(),
        ]);
    }

    protected function addPostAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        EntityManagerInterface $entityManager,
        ObjectRouterInterface $objectRouter,
        City $city,
        FormInterface $form
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
            'dateTime' => new \DateTime(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    public function editAction(Request $request, UserInterface $user = null, Ride $ride, ObjectRouterInterface $objectRouter): Response
    {
        $form = $this->createForm(RideType::class, $ride, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_edit'),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $user, $ride, $ride->getCity(), $form, $objectRouter);
        } else {
            return $this->editGetAction($request, $user, $ride, $ride->getCity(), $form, $objectRouter);
        }
    }

    protected function editGetAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        City $city,
        FormInterface $form,
        ObjectRouterInterface $objectRouter
    ): Response {
        return $this->render('RideManagement/edit.html.twig', [
            'ride' => $ride,
            'city' => $city,
            'form' => $form->createView(),
            'dateTime' => new \DateTime(),
        ]);
    }

    protected function editPostAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        City $city,
        FormInterface $form,
        ObjectRouterInterface $objectRouter
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride
                ->setUpdatedAt(new \DateTime())
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
            'dateTime' => new \DateTime(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    public function socialPreviewAction(
        EntityManagerInterface $entityManager,
        Request $request,
        UserInterface $user = null,
        ObjectRouterInterface $objectRouter,
        Ride $ride
    ): Response {
        $form = $this->createForm(RideSocialPreviewType::class, $ride, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_ride_socialpreview'),
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->socialPreviewPostAction($entityManager, $request, $user, $ride, $form);
        } else {
            return $this->socialPreviewGetAction($entityManager, $request, $user, $ride, $form);
        }
    }

    protected function socialPreviewGetAction(
        EntityManagerInterface $entityManager,
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        Form $form
    ): Response {
        return $this->render('RideManagement/social_preview.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function socialPreviewPostAction(
        EntityManagerInterface $entityManager,
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        Form $form
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride
                ->setUpdatedAt(new \DateTime())
                ->setUser($user);

            $entityManager->flush();

            $request->getSession()->getFlashBag()->add('success', 'Änderungen gespeichert!');
        }

        return $this->socialPreviewGetAction($entityManager, $request, $user, $ride, $form);
    }

    #[IsGranted('ROLE_USER')]
    public function disableAction(Request $request, ManagerRegistry $registry, UserInterface $user = null, Ride $ride, ObjectRouterInterface $objectRouter): RedirectResponse
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
    public function enableAction(ManagerRegistry $registry, UserInterface $user = null, Ride $ride, ObjectRouterInterface $objectRouter): RedirectResponse
    {
        $ride->setEnabled(true)
            ->setDisabledReason(null)
            ->setDisabledReasonMessage(null);

        $registry->getManager()->flush();

        return $this->redirect($objectRouter->generate($ride));
    }
}
