<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Form\Type\CityCycleType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class CityCycleManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function addAction(Request $request, UserInterface $user = null, City $city, ObjectRouterInterface $objectRouter): Response
    {
        $cityCycle = new CityCycle();
        $cityCycle
            ->setCity($city)
            ->setUser($user);

        $form = $this->createForm(CityCycleType::class, $cityCycle, [
            'action' => $objectRouter->generate($city, 'caldera_criticalmass_citycycle_add'),
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $cityCycle, $form, $objectRouter);
        } else {
            return $this->addGetAction($request, $cityCycle, $form, $objectRouter);
        }
    }

    protected function addGetAction(Request $request, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('CityCycle/edit.html.twig', [
            'city' => $cityCycle->getCity(),
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    protected function addPostAction(Request $request, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        $city = $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($city, 'caldera_criticalmass_citycycle_list'));
        }

        return $this->render('CityCycle/edit.html.twig', [
            'city' => $city,
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function editAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, ObjectRouterInterface $objectRouter): Response
    {
        $cityCycle->setUser($user);

        $form = $this->createForm(CityCycleType::class, $cityCycle, [
            'action' => $objectRouter->generate($cityCycle, 'caldera_criticalmass_citycycle_edit'),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $user, $cityCycle, $form, $objectRouter);
        } else {
            return $this->editGetAction($request, $user, $cityCycle, $form, $objectRouter);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        return $this->render('CityCycle/edit.html.twig', [
            'city' => $cityCycle->getCity(),
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    protected function editPostAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, FormInterface $form, ObjectRouterInterface $objectRouter): Response
    {
        $city = $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $form = $this->createForm(CityCycleType::class, $cityCycle, [
                'action' => $objectRouter->generate($cityCycle, 'caldera_criticalmass_citycycle_edit'),
            ]);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->render('CityCycle/edit.html.twig', [
            'city' => $city,
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function disableAction(CityCycle $cityCycle, ManagerRegistry $managerRegistry, ObjectRouterInterface $objectRouter): Response
    {
        $manager = $managerRegistry->getManager();

        if ($cityCycle->getRides()->count() > 0) {
            if (!$cityCycle->getValidFrom()) {
                $cityCycle->setValidFrom($cityCycle->getCreatedAt());
            }

            if (!$cityCycle->getValidUntil()) {
                $cityCycle->setValidUntil(new \DateTime());
            }

            $cityCycle->setDisabledAt(new \DateTime());
        } elseif (0 === $cityCycle->getRides()->count()) {
            $manager->remove($cityCycle);
        }

        $manager->flush();

        return $this->redirect($objectRouter->generate($cityCycle->getCity(), 'caldera_criticalmass_citycycle_list'));
    }
}
