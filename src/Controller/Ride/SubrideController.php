<?php declare(strict_types=1);

namespace App\Controller\Ride;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Subride;
use App\Form\Type\SubrideType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class SubrideController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function addAction(Request $request, Ride $ride, UserInterface $user, ObjectRouterInterface $objectRouter): Response
    {
        $subride = new Subride();
        $subride
            ->setDateTime($ride->getDateTime())
            ->setRide($ride)
            ->setUser($user);

        $form = $this->createForm(SubrideType::class, $subride, [
            'action' => $objectRouter->generate($ride, 'caldera_criticalmass_subride_add'),
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $subride, $form, $objectRouter);
        } else {
            return $this->addGetAction($request, $subride, $form, $objectRouter);
        }
    }

    protected function addGetAction(Request $request, Subride $subride, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('Subride/edit.html.twig', [
            'subride' => null,
            'form' => $form->createView(),
            'city' => $subride->getRide()->getCity(),
            'ride' => $subride->getRide(),
        ]);
    }

    protected function addPostAction(Request $request, Subride $subride, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        $form->handleRequest($request);

        $actionUrl = $objectRouter->generate($subride->getRide(), 'caldera_criticalmass_subride_add');

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($form->getData());
            $em->flush();

            $actionUrl = $objectRouter->generate($subride, 'caldera_criticalmass_subride_edit');
        }

        /* As we have created our new ride, we serve the user the new "edit ride form". Normally it would be enough
        just to change the action url of the form, but we are far to stupid for this hack. */
        $form = $this->createForm(SubrideType::class, $subride, [
            'action' => $actionUrl
        ]);

        // QND: this is a try to serve an instance of the new created subride to get the marker to the right place
        return $this->render('Subride/edit.html.twig', [
            'subride' => $subride,
            'form' => $form->createView(),
            'city' => $subride->getRide()->getCity(),
            'ride' => $subride->getRide(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @ParamConverter("subride", class="App:Subride", options={"id" = "subrideId"})
     */
    public function editAction(Request $request, Subride $subride, ObjectRouterInterface $objectRouter): Response
    {
        $form = $this->createForm(SubrideType::class, $subride, [
            'action' => $objectRouter->generate($subride, 'caldera_criticalmass_subride_edit'),
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->editPostAction($request, $subride, $form);
        } else {
            return $this->editGetAction($request, $subride, $form);
        }
    }

    protected function editGetAction(Request $request, Subride $subride, FormInterface $form): Response
    {
        return $this->render('Subride/edit.html.twig', [
            'subride' => null,
            'form' => $form->createView(),
            'city' => $subride->getRide()->getCity(),
            'ride' => $subride->getRide(),
        ]);
    }

    protected function editPostAction(Request $request, Subride $subride, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->render('Subride/edit.html.twig', [
            'ride' => $subride->getRide(),
            'city' => $subride->getRide()->getCity(),
            'subride' => $subride,
            'form' => $form->createView(),
            'dateTime' => new \DateTime(),
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function preparecopyAction(
        RideRepository $rideRepository,
        Ride $ride
    ): Response {
        $oldRide = $rideRepository->getPreviousRideWithSubrides($ride);

        return $this->render('Subride/preparecopy.html.twig', [
            'oldRide' => $oldRide,
            'newRide' => $ride,
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER')")
     * @ParamConverter("oldRide", class="App:Ride")
     * @ParamConverter("newDate", options={"format": "Y-m-d"})
     */
    public function copyAction(
        Ride $oldRide,
        \DateTime $newDate,
        ObjectRouterInterface $objectRouter,
        RideRepository $rideRepository
    ): Response {
        $ride = $rideRepository->findCityRideByDate($oldRide->getCity(), $newDate);

        $em = $this->managerRegistry->getManager();

        /** @var Subride $oldSubride */
        foreach ($oldRide->getSubrides() as $oldSubride) {
            $newSubride = clone $oldSubride;
            $newSubride->setUser($this->getUser());
            $newSubride->setRide($ride);

            $newSubrideDateTime = new \DateTime($ride->getDateTime()->format('Y-m-d') . ' ' . $oldSubride->getDateTime()->format('H:i:s'));
            $newSubride->setDateTime($newSubrideDateTime);

            $em->persist($newSubride);
        }

        $em->flush();

        return $this->redirect($objectRouter->generate($ride, 'caldera_criticalmass_ride_show'));
    }
}
