<?php

namespace AppBundle\Controller\Ride;

use AppBundle\Form\Type\RideSocialPreviewType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use AppBundle\Form\Type\RideType;
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
     * @ParamConverter("city", class="AppBundle:City")
     */
    public function addAction(Request $request, UserInterface $user, EntityManagerInterface $entityManager, City $city): Response
    {
        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setUser($user);

        $form = $this->createForm(RideType::class, $ride, [
            'action' => $this->generateUrl('caldera_criticalmass_ride_add', [
                'citySlug' => $city->getMainSlugString(),
            ])
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            return $this->addPostAction($request, $user, $ride, $entityManager, $city, $form);
        } else {
            return $this->addGetAction($request, $user, $ride, $entityManager, $city, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user, Ride $ride, EntityManagerInterface $entityManager, City $city, FormInterface $form): Response
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render('AppBundle:RideManagement:edit.html.twig', [
            'hasErrors' => null,
            'ride' => null,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => new \DateTime(),
            'oldRides' => $oldRides,
        ]);
    }

    protected function addPostAction(
        Request $request,
        UserInterface $user,
        Ride $ride,
        EntityManagerInterface $entityManager,
        City $city,
        FormInterface $form
    ): Response {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $ride = $form->getData();

            $entityManager->persist($ride);
            $entityManager->flush();

            // TODO: remove also this
            $hasErrors = false;

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(RideType::class, $ride, [
                'action' => $this->generateUrl('caldera_criticalmass_ride_edit', [
                    'citySlug' => $city->getMainSlugString(),
                    'rideDate' => $ride->getFormattedDate()
                ])
            ]);
        } elseif ($form->isSubmitted()) {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('AppBundle:RideManagement:edit.html.twig', [
            'hasErrors' => $hasErrors,
            'ride' => $ride,
            'form' => $form->createView(),
            'city' => $city,
            'dateTime' => new \DateTime(),
            'oldRides' => $oldRides,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function editAction(Request $request, UserInterface $user, Ride $ride): Response
    {
        $form = $this->createForm(
            RideType::class,
            $ride,
            array(
                'action' => $this->generateUrl('caldera_criticalmass_ride_edit',
                    array(
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getDateTime()->format('Y-m-d')
                    )
                )
            )
        );

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $user, $ride, $ride->getCity(), $form);
        } else {
            return $this->editGetAction($request, $user, $ride, $ride->getCity(), $form);
        }
    }

    protected function editGetAction(
        Request $request,
        UserInterface $user,
        Ride $ride,
        City $city,
        Form $form
    ): Response {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'AppBundle:RideManagement:edit.html.twig',
            array(
                'ride' => $ride,
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => null,
                'dateTime' => new \DateTime(),
                'oldRides' => $oldRides
            )
        );
    }

    protected function editPostAction(
        Request $request,
        UserInterface $user,
        Ride $ride,
        City $city,
        Form $form
    ): Response {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid()) {
            $ride
                ->setUpdatedAt(new \DateTime())
                ->setUser($user);

            $this->getDoctrine()->getManager()->flush();

            // TODO: remove also this
            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render(
            'AppBundle:RideManagement:edit.html.twig',
            array(
                'ride' => $ride,
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'dateTime' => new \DateTime(),
                'oldRides' => $oldRides
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function facebookUpdateAction(Ride $ride): Response
    {
        /**
         * @var FacebookEventRideApi $fera
         */
        $fera = $this->get('caldera.criticalmass.facebookapi.eventride');

        $facebookRide = $fera->createRideForRide($ride);

        $form = $this->createForm(
            RideType::class,
            $ride,
            array(
                'action' => $this->generateUrl('caldera_criticalmass_ride_edit',
                    array(
                        'citySlug' => $ride->getCity()->getSlug(),
                        'rideDate' => $ride->getFormattedDate()
                    )
                )
            )
        );

        return $this->render(
            'AppBundle:RideManagement:facebook_update.html.twig',
            [
                'city' => $ride->getCity(),
                'ride' => $ride,
                'facebookRide' => $facebookRide,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function socialPreviewAction(
        EntityManagerInterface $entityManager,
        Request $request,
        UserInterface $user,
        Ride $ride
    ): Response {
        $form = $this->createForm(RideSocialPreviewType::class, $ride, [
            'action' => $this->generateUrl('caldera_criticalmass_ride_socialpreview', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getDateTime()->format('Y-m-d')
            ])
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
        UserInterface $user,
        Ride $ride,
        Form $form
    ): Response {
        return $this->render('AppBundle:RideManagement:social_preview.html.twig', [
            'ride' => $ride,
            'form' => $form->createView(),
        ]);
    }

    protected function socialPreviewPostAction(
        EntityManagerInterface $entityManager,
        Request $request,
        UserInterface $user,
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
