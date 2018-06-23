<?php

namespace AppBundle\Controller\Ride;

use AppBundle\Entity\Ride;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Subride;
use AppBundle\Form\Type\SubrideType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SubrideController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function addAction(Request $request, Ride $ride): Response
    {
        $subride = new Subride();
        $subride
            ->setDateTime($ride->getDateTime())
            ->setRide($ride)
            ->setUser($this->getUser());

        $form = $this->createForm(SubrideType::class, $subride, [
            'action' => $this->generateUrl('caldera_criticalmass_subride_add', [
                'citySlug' => $ride->getCity()->getMainSlugString(),
                'rideDate' => $ride->getFormattedDate(),
            ])
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $subride, $form);
        } else {
            return $this->addGetAction($request, $subride, $form);
        }
    }

    protected function addGetAction(Request $request, Subride $subride, Form $form): Response
    {
        return $this->render('AppBundle:Subride:edit.html.twig', [
            'hasErrors' => null,
            'subride' => null,
            'form' => $form->createView(),
            'city' => $subride->getRide()->getCity(),
            'ride' => $subride->getRide(),
        ]);
    }

    protected function addPostAction(Request $request, Subride $subride, Form $form): Response
    {
        $form->handleRequest($request);

        $hasErrors = true;
        $actionUrl = $this->generateUrl('caldera_criticalmass_subride_add', [
            'citySlug' => $subride->getRide()->getCity()->getMainSlugString(),
            'rideDate' => $subride->getRide()->getFormattedDate(),
        ]);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;

            $actionUrl = $this->generateUrl('caldera_criticalmass_subride_edit', [
                'citySlug' => $subride->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $subride->getRide()->getFormattedDate(),
                'subrideId' => $subride->getId(),
            ]);
        }

        /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
        just to change the action url of the form, but we are far to stupid for this hack. */
        $form = $this->createForm(SubrideType::class, $subride, [
            'action' => $actionUrl
        ]);

        // QND: this is a try to serve an instance of the new created subride to get the marker to the right place
        return $this->render('AppBundle:Subride:edit.html.twig', [
            'hasErrors' => $hasErrors,
            'subride' => $subride,
            'form' => $form->createView(),
            'city' => $subride->getRide()->getCity(),
            'ride' => $subride->getRide(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("subride", class="AppBundle:Subride", options={"id" = "subrideId"})
     */
    public function editAction(Request $request, Subride $subride): Response
    {
        $form = $this->createForm(SubrideType::class, $subride, [
            'action' => $this->generateUrl('caldera_criticalmass_subride_edit', [
                'citySlug' => $subride->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $subride->getRide()->getFormattedDate(),
                'subrideId' => $subride->getId()
            ])
        ]);

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $subride, $form);
        } else {
            return $this->editGetAction($request, $subride, $form);
        }
    }

    protected function editGetAction(Request $request, Subride $subride, Form $form): Response
    {
        return $this->render('AppBundle:Subride:edit.html.twig', [
            'hasErrors' => null,
            'subride' => null,
            'form' => $form->createView(),
            'city' => $subride->getRide()->getCity(),
            'ride' => $subride->getRide()
        ]);
    }

    protected function editPostAction(Request $request, Subride $subride, Form $form): Response
    {
        $form->handleRequest($request);

        // TODO: remove this shit and test the validation in the template
        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // TODO: remove also this
            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            // TODO: remove even more shit
            $hasErrors = true;
        }

        return $this->render('AppBundle:Subride:edit.html.twig', [
            'ride' => $subride->getRide(),
            'city' => $subride->getRide()->getCity(),
            'subride' => $subride,
            'form' => $form->createView(),
            'hasErrors' => $hasErrors,
            'dateTime' => new \DateTime(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function preparecopyAction(Ride $ride): Response
    {
        $oldRide = $this->getRideRepository()->getPreviousRideWithSubrides($ride);

        return $this->render('AppBundle:Subride:preparecopy.html.twig', [
            'oldRide' => $oldRide,
            'newRide' => $ride
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     * @ParamConverter("oldDate", options={"format": "Y-m-d"})
     */
    public function copyAction(Ride $ride, \DateTime $oldDate): Response
    {
        $oldRide = $this->getRideRepository()->findCityRideByDate($ride->getCity(), $oldDate);

        $em = $this->getDoctrine()->getManager();

        /**
         * @var Subride $oldSubride
         */
        foreach ($oldRide->getSubrides() as $oldSubride) {
            $newSubride = clone $oldSubride;
            $newSubride->setUser($this->getUser());
            $newSubride->setRide($ride);

            $newSubrideDateTime = new \DateTime($ride->getDateTime()->format('Y-m-d') . ' ' . $oldSubride->getDateTime()->format('H:i:s'));
            $newSubride->setDateTime($newSubrideDateTime);

            $em->persist($newSubride);
        }

        $em->flush();

        return $this->redirectToRoute('caldera_criticalmass_ride_show', [
            'citySlug' => $ride->getCity()->getMainSlugString(),
            'rideDate' => $ride->getFormattedDate()
        ]);
    }
}
