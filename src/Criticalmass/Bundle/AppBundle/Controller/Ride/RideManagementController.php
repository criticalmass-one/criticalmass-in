<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Ride;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Component\Facebook\FacebookEventRideApi;
use Criticalmass\Bundle\AppBundle\Form\Type\RideType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class RideManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setUser($user)
        ;

        $form = $this->createForm(
            RideType::class,
            $ride,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_desktop_ride_add',
                    [
                        'citySlug' => $city->getMainSlugString()
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->addPostAction($request, $user, $ride, $city, $form);
        } else {
            return $this->addGetAction($request, $user, $ride, $city, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user, Ride $ride, City $city, Form $form): Response
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'AppBundle:RideManagement:edit.html.twig',
            [
                'hasErrors' => null,
                'ride' => null,
                'form' => $form->createView(),
                'city' => $city,
                'dateTime' => new \DateTime(),
                'oldRides' => $oldRides
            ]
        );
    }

    protected function addPostAction(Request $request, UserInterface $user, Ride $ride, City $city, Form $form): Response
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            // TODO: remove also this
            $hasErrors = false;

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(
                RideType::class,
                $ride,
                [
                    'action' => $this->generateUrl(
                        'caldera_criticalmass_desktop_ride_edit',
                        [
                            'citySlug' => $city->getMainSlugString(),
                            'rideDate' => $ride->getFormattedDate()
                        ]
                    )
                ]
            );
        } elseif ($form->isSubmitted()) {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render(
            'AppBundle:RideManagement:edit.html.twig',
            array(
                'hasErrors' => $hasErrors,
                'ride' => $ride,
                'form' => $form->createView(),
                'city' => $city,
                'dateTime' => new \DateTime(),
                'oldRides' => $oldRides
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, UserInterface $user, string $citySlug, string $rideDate): Response
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($rideDate);
        $ride = $this->getCheckedRide($city, $rideDateTime);

        $form = $this->createForm(
            RideType::class,
            $ride,
            array(
                'action' => $this->generateUrl('caldera_criticalmass_desktop_ride_edit',
                    array(
                        'citySlug' => $city->getMainSlugString(),
                        'rideDate' => $ride->getDateTime()->format('Y-m-d')
                    )
                )
            )
        );

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $user, $ride, $city, $form);
        } else {
            return $this->editGetAction($request, $user, $ride, $city, $form);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user, Ride $ride, City $city, Form $form): Response
    {
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

    protected function editPostAction(Request $request, UserInterface $user, Ride $ride, City $city, Form $form): Response
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid()) {
            $ride
                ->setUpdatedAt(new \DateTime())
                ->setUser($user)
            ;

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
     */
    public function facebookUpdateAction(Request $request, string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        /**
         * @var FacebookEventRideApi $fera
         */
        $fera = $this->get('caldera.criticalmass.facebookapi.eventride');

        $facebookRide = $fera->createRideForRide($ride);

        $form = $this->createForm(
            new RideType(),
            $ride,
            array(
                'action' => $this->generateUrl('caldera_criticalmass_desktop_ride_edit',
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
}
