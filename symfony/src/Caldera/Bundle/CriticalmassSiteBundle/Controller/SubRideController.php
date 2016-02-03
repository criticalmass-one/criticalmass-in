<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\SubrideType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Subride;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubrideController extends AbstractController
{
    public function addAction(Request $request, $citySlug, $rideDate)
    {
        if (!$this->getUser())
        {
            throw new AccessDeniedHttpException('Du musst angemeldet sein, um eine Minimass erstellen zu können.');
        }

        $citySlugObj = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlugObj)
        {
            throw new NotFoundHttpException('Wir haben leider keine Stadt in der Datenbank, die sich mit '.$citySlug.' identifiziert.');
        }

        $city = $citySlugObj->getCity();

        $rideDateTime = new \DateTime($rideDate);

        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findCityRideByDate($city, $rideDateTime);

        if (!$ride)
        {
            throw new NotFoundHttpException('Wir haben leider keine Tour in '.$city->getCity().' am '.$rideDateTime->format('d. m. Y').' gefunden.');
        }

        $subRide = new SubRide();
        $subRide->setDateTime($ride->getDateTime());
        $subRide->setRide($ride);
        $subRide->setUser($this->getUser());

        $form = $this->createForm(new SubRideType(), $subRide, array('action' => $this->generateUrl('caldera_criticalmass_desktop_subride_add', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $rideDate))));

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;

            /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
            just to change the action url of the form, but we are far to stupid for this hack. */
            $form = $this->createForm(new RideType(), $ride, array('action' => $this->generateUrl('caldera_criticalmass_desktop_ride_edit', array('citySlug' => $city->getMainSlugString(), 'rideDate' => $ride->getFormattedDate()))));

            // QND: this is a try to serve an instance of the new created subride to get the marker to the right place
            return $this->render('CalderaCriticalmassDesktopBundle:SubRide:edit.html.twig', array('hasErrors' => $hasErrors, 'subRide' => $subRide, 'form' => $form->createView(), 'city' => $city, 'ride' => $ride));
        }
        elseif ($form->isSubmitted())
        {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('CalderaCriticalmassDesktopBundle:SubRide:edit.html.twig', array('hasErrors' => $hasErrors, 'subRide' => null, 'form' => $form->createView(), 'city' => $city, 'ride' => $ride));
    }

    public function editAction(Request $request, $citySlug, $rideDate, $subrideId)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $subride = $this->getSubrideRepository()->find($subrideId);

        if (!$subride->getRide()->equals($ride)) {
            throw new NotFoundHttpException();
        }

        $archiveRide = clone $subride;
        $archiveRide->setArchiveUser($this->getUser());
        $archiveRide->setArchiveParent($subride);
        $archiveRide->setIsArchived(true);
        $archiveRide->setArchiveDateTime(new \DateTime());

        $form = $this->createForm(
            new SubrideType(),
            $subride,
            [
                'action' => $this->generateUrl('caldera_criticalmass_desktop_subride_edit',
                    [
                        'citySlug' => $ride->getCity()->getMainSlugString(),
                        'rideDate' => $ride->getFormattedDate(),
                        'subrideId' => $subride->getId()
                    ]
                )
            ]
        );

        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->persist($archiveRide);
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;
        }
        elseif ($form->isSubmitted())
        {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Subride:edit.html.twig',
            [
                'ride' => $subride->getRide(),
                'subride' => $subride,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'dateTime' => new \DateTime()
            ]
        );
    }

    public function preparecopyAction(Request $request, $citySlug, $rideDate)
    {
        $newRide = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $oldRide = $this->getRideRepository()->getPreviousRideWithSubrides($newRide);

        return $this->render('CalderaCriticalmassSiteBundle:Subride:preparecopy.html.twig', array('oldRide' => $oldRide, 'newRide' => $newRide));
    }

    public function copyAction(Request $request, $citySlug, $oldDate, $newDate)
    {
        $newRide = $this->getCheckedCitySlugRideDateRide($citySlug, $newDate);
        $oldDateTime = $this->getCheckedDateTime($oldDate);

        $oldRide = $this->getRideRepository()->findCityRideByDate($newRide->getCity(), $oldDateTime);

        $em = $this->getDoctrine()->getManager();

        /**
         * @var Subride $oldSubride
         */
        foreach ($oldRide->getSubrides() as $oldSubride)
        {
            $newSubride = clone $oldSubride;
            $newSubride->setUser($this->getUser());
            $newSubride->setRide($newRide);

            $newSubrideDateTime = new \DateTime($newRide->getDateTime()->format('Y-m-d').' '.$oldSubride->getDateTime()->format('H:i:s'));
            $newSubride->setDateTime($newSubrideDateTime);

            $em->persist($newSubride);
        }

        $em->flush();

        return $this->redirectToRoute(
            'caldera_criticalmass_desktop_ride_show',
            [
                'citySlug' => $newRide->getCity()->getMainSlugString(),
                'rideDate' => $newRide->getFormattedDate()
            ]
        );
    }
}
