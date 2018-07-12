<?php

namespace App\Controller\City;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Form\Type\CityCycleType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class CityCycleController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function listAction(City $city): Response
    {
        return $this->render('App:CityCycle:list.html.twig', [
            'cycles' => $this->getCityCycleRepository()->findByCity($city),
            'city' => $city,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function addAction(Request $request, UserInterface $user = null, City $city): Response
    {
        $cityCycle = new CityCycle();
        $cityCycle
            ->setCity($city)
            ->setUser($user);

        $form = $this->createForm(CityCycleType::class, $cityCycle, [
            'action' => $this->generateObjectUrl($city, 'caldera_criticalmass_citycycle_add'),
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addPostAction($request, $cityCycle, $form);
        } else {
            return $this->addGetAction($request, $cityCycle, $form);
        }
    }

    protected function addGetAction(Request $request, CityCycle $cityCycle, FormInterface $form): Response
    {
        return $this->render('App:CityCycle:edit.html.twig', [
            'city' => $cityCycle->getCity(),
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    protected function addPostAction(Request $request, CityCycle $cityCycle, FormInterface $form): Response
    {
        $city = $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirectToObject($city, 'caldera_criticalmass_citycycle_list');
        }

        return $this->render('App:CityCycle:edit.html.twig', [
            'city' => $city,
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function editAction(Request $request, UserInterface $user = null, CityCycle $cityCycle): Response
    {
        $cityCycle->setUser($user);

        $form = $this->createForm(CityCycleType::class, $cityCycle, [
            'action' => $this->generateObjectUrl($cityCycle, 'caldera_criticalmass_citycycle_edit'),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->editPostAction($request, $user, $cityCycle, $form);
        } else {
            return $this->editGetAction($request, $user, $cityCycle, $form);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, FormInterface $form): Response
    {
        return $this->render('App:CityCycle:edit.html.twig', [
            'city' => $cityCycle->getCity(),
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    protected function editPostAction(Request $request, UserInterface $user = null, CityCycle $cityCycle, FormInterface $form): Response
    {
        $city = $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $form = $this->createForm(CityCycleType::class, $cityCycle, [
                'action' => $this->generateObjectUrl($cityCycle, 'caldera_criticalmass_citycycle_edit'),
            ]);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');
        }

        return $this->render('App:CityCycle:edit.html.twig', [
            'city' => $city,
            'cityCycle' => $cityCycle,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle", options={"id" = "cycleId"})
     */
    public function disableAction(CityCycle $cityCycle, ObjectManager $objectManager): Response
    {
        if ($cityCycle->getRides()->count() > 0) {
            if (!$cityCycle->getValidFrom()) {
                $cityCycle->setValidFrom($cityCycle->getCreatedAt());
            }

            if (!$cityCycle->getValidUntil()) {
                $cityCycle->setValidUntil(new \DateTime());
            }

            $cityCycle->setDisabledAt(new \DateTime());
        } elseif (0 === $cityCycle->getRides()->count()) {
            $objectManager->remove($cityCycle);
        }

        $objectManager->flush();

        return $this->redirectToObject($city, 'caldera_criticalmass_citycycle_list');
    }
}
