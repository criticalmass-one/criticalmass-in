<?php

namespace App\Controller\Ride;

use App\Form\Type\RideSocialPreviewType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\Ride;
use App\Form\Type\RideType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RideManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function addAction(Request $request, UserInterface $user = null, EntityManagerInterface $entityManager, City $city): Response
    {
        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setUser($user);

        $form = $this->createForm(RideType::class, $ride, [
            'action' => $this->generateObjectUrl($city,'caldera_criticalmass_ride_add'),
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $user, $ride, $entityManager, $city, $form);
        } else {
            return $this->addGetAction($request, $user, $ride, $entityManager, $city, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user = null, Ride $ride, EntityManagerInterface $entityManager, City $city, FormInterface $form): Response
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render('App:RideManagement:edit.html.twig', [
            'ride' => null,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => new \DateTime(),
            'oldRides' => $oldRides,
        ]);
    }

    protected function addPostAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        EntityManagerInterface $entityManager,
        City $city,
        FormInterface $form
    ): Response {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride = $form->getData();

            $entityManager->persist($ride);
            $entityManager->flush();

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(RideType::class, $ride, [
                'action' => $this->redirectToObject($ride, 'caldera_criticalmass_ride_edit'),
            ]);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->render('App:RideManagement:edit.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => new \DateTime(),
            'oldRides' => $oldRides,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function editAction(Request $request, UserInterface $user = null, Ride $ride): Response
    {
        $form = $this->createForm(RideType::class, $ride, [
            'action' => $this->generateObjectUrl($ride, 'caldera_criticalmass_ride_edit'),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $user, $ride, $ride->getCity(), $form);
        } else {
            return $this->editGetAction($request, $user, $ride, $ride->getCity(), $form);
        }
    }

    protected function editGetAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        City $city,
        FormInterface $form
    ): Response {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render('App:RideManagement:edit.html.twig', [
            'ride' => $ride,
            'city' => $city,
            'form' => $form->createView(),
            'dateTime' => new \DateTime(),
            'oldRides' => $oldRides
        ]);
    }

    protected function editPostAction(
        Request $request,
        UserInterface $user = null,
        Ride $ride,
        City $city,
        FormInterface $form
    ): Response {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ride
                ->setUpdatedAt(new \DateTime())
                ->setUser($user);

            $this->getDoctrine()->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->render('App:RideManagement:edit.html.twig', [
            'ride' => $ride,
            'city' => $city,
            'form' => $form->createView(),
            'dateTime' => new \DateTime(),
            'oldRides' => $oldRides,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="App:Ride")
     */
    public function socialPreviewAction(
        EntityManagerInterface $entityManager,
        Request $request,
        UserInterface $user = null,
        Ride $ride
    ): Response {
        $form = $this->createForm(RideSocialPreviewType::class, $ride, [
            'action' => $this->generateObjectUrl($ride, 'caldera_criticalmass_ride_socialpreview'),
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
        return $this->render('App:RideManagement:social_preview.html.twig', [
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
}
