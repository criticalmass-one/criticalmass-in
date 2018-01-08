<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use Criticalmass\Bundle\AppBundle\Entity\CityCycle;
use Criticalmass\Bundle\AppBundle\Form\Type\CityCycleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class CityCycleController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);

        $cycles = $this->getCityCycleRepository()->findByCity($city);

        return $this->render(
            'AppBundle:CityCycle:list.html.twig',
            [
                'cycles' => $cycles,
                'city' => $city,
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCheckedCity($citySlug);
        $cityCycle = new CityCycle();
        $cityCycle
            ->setCity($city)
            ->setUser($user)
        ;

        $form = $this->createForm(
            CityCycleType::class,
            $cityCycle,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_citycycle_add',
                    [
                        'citySlug' => $citySlug,
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->addPostAction($request, $user, $cityCycle, $form);
        } else {
            return $this->addGetAction($request, $user, $cityCycle, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user, CityCycle $cityCycle, Form $form): Response
    {
        return $this->render(
            'AppBundle:CityCycle:edit.html.twig',
            [
                'city' => $cityCycle->getCity(),
                'cityCycle' => $cityCycle,
                'form' => $form->createView(),
            ]
        );
    }

    protected function addPostAction(Request $request,UserInterface $user, CityCycle $cityCycle, Form $form): Response
    {
        $city =  $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $form = $this->createForm(
                CityCycleType::class,
                $cityCycle,
                [
                    'action' => $this->generateUrl(
                        'caldera_criticalmass_citycycle_add',
                        [
                            'citySlug' => $city->getMainSlugString(),
                        ]
                    )
                ]
            );

            return $this->redirectToRoute('caldera_criticalmass_citycycle_list', ['citySlug' => $city->getMainSlugString()]);
        }

        return $this->render(
            'AppBundle:CityCycle:edit.html.twig',
            [
                'city' => $city,
                'cityCycle' => $cityCycle,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, UserInterface $user, string $citySlug, int $cycleId): Response
    {
        /** @var CityCycle $cityCycle */
        $cityCycle = $this->getCityCycleRepository()->find($cycleId);

        if (!$cityCycle) {
            throw $this->createNotFoundException();
        }

        $cityCycle
            ->setUser($user)
        ;

        $form = $this->createForm(
            CityCycleType::class,
            $cityCycle,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_citycycle_edit',
                    [
                        'citySlug' => $citySlug,
                        'cycleId' => $cycleId,
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->addPostAction($request, $user, $cityCycle, $form);
        } else {
            return $this->addGetAction($request, $user, $cityCycle, $form);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user, CityCycle $cityCycle, Form $form): Response
    {
        return $this->render(
            'AppBundle:CityCycle:edit.html.twig',
            [
                'city' => $cityCycle->getCity(),
                'cityCycle' => $cityCycle,
                'form' => $form->createView(),
            ]
        );
    }

    protected function editPostAction(Request $request,UserInterface $user, CityCycle $cityCycle, Form $form): Response
    {
        $city =  $cityCycle->getCity();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($cityCycle);
            $em->flush();

            $form = $this->createForm(
                CityCycleType::class,
                $cityCycle,
                [
                    'action' => $this->generateUrl(
                        'caldera_criticalmass_citycycle_edit',
                        [
                            'citySlug' => $city->getMainSlugString(),
                            'cycleId' => $cityCycle->getId(),
                        ]
                    )
                ]
            );

            return $this->render(
                'AppBundle:CityCycle:edit.html.twig',
                [
                    'city' => $city,
                    'cityCycle' => $cityCycle,
                    'form' => $form->createView(),
                ]
            );
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render(
            'AppBundle:CityCycle:edit.html.twig',
            [
                'city' => $city,
                'cityCycle' => $cityCycle,
                'form' => $form->createView(),
            ]
        );
    }
}
