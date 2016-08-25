<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Facebook\FacebookEventRideApi;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideEstimateType;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\RideEstimate;
use Caldera\Bundle\CalderaBundle\Entity\Weather;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class RideManagementController extends AbstractController
{
    public function addAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $ride = new Ride();
        $ride->setCity($city);
        $ride->setUser($this->getUser());

        $form = $this->createForm(
            new RideType(),
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
            return $this->addPostAction($request, $ride, $city, $form);
        } else {
            return $this->addGetAction($request, $ride, $city, $form);
        }
    }

    protected function addGetAction(Request $request, Ride $ride, City $city, Form $form)
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:RideManagement:edit.html.twig',
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

    protected function addPostAction(Request $request, Ride $ride, City $city, Form $form)
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid() && !$city->hasRideAtMonthDay($ride->getDateTime())) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(
                new RideType(), 
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
            'CalderaCriticalmassSiteBundle:RideManagement:edit.html.twig',
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

    public function editAction(Request $request, $citySlug, $rideDate)
    {
        $city = $this->getCheckedCity($citySlug);
        $rideDateTime = $this->getCheckedDateTime($rideDate);
        $ride = $this->getCheckedRide($city, $rideDateTime);

        $form = $this->createForm(
            new RideType(), 
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
            return $this->editPostAction($request, $ride, $city, $form);
        } else {
            return $this->editGetAction($request, $ride, $city, $form);
        }
    }

    protected function editGetAction(Request $request, Ride $ride, City $city, Form $form)
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:RideManagement:edit.html.twig',
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
    
    protected function editPostAction(Request $request, Ride $ride, City $city, Form $form)
    {
        $oldRides = $this->getRideRepository()->findRidesForCity($city);
        
        $archiveRide = clone $ride;
        $archiveRide->setArchiveUser($this->getUser());
        $archiveRide->setArchiveParent($ride);

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->persist($archiveRide);
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:RideManagement:edit.html.twig',
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

    public function facebookUpdateAction(Request $request, $citySlug, $rideDate)
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
            'CalderaCriticalmassSiteBundle:RideManagement:facebookUpdate.html.twig',
            [
                'city' => $ride->getCity(),
                'ride' => $ride,
                'facebookRide' => $facebookRide,
                'form' => $form->createView()
            ]
        );
    }
}
