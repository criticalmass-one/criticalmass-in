<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\CitySlugGenerator\CitySlugGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\StandardCityType;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CityType;
use Caldera\Bundle\CalderaBundle\Entity\Region;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityManagementController extends AbstractController
{
    public function addAction(Request $request, $slug1, $slug2, $slug3)
    {
        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->findOneBySlug($slug3);

        $city = new City();
        $city->setRegion($region);
        $city->setUser($this->getUser());

        $form = $this->createForm(
            new StandardCityType(),
            $city,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_desktop_city_add',
                    [
                        'slug1' => $slug1,
                        'slug2' => $slug2,
                        'slug3' => $slug3
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->addPostAction($request, $city, $region, $form);
        } else {
            return $this->addGetAction($request, $city, $region, $form);
        }
    }

    protected function addGetAction(Request $request, City $city, Region $region, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:CityManagement:edit.html.twig',
            [
                'city' => null,
                'form' => $form->createView(),
                'hasErrors' => null,
                'country' => $region->getParent()->getName(),
                'state' => $region->getName(),
                'region' => $region
            ]
        );
    }

    protected function addPostAction(Request $request, City $city, Region $region, Form $form)
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $csg = new CitySlugGenerator($city);
            $citySlug = $csg->execute();
            $city->addSlug($citySlug);

            $em->persist($citySlug);
            $em->persist($city);
            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(
                new StandardCityType(),
                $city,
                [
                    'action' => $this->generateUrl(
                        'caldera_criticalmass_desktop_city_edit',
                        [
                            'citySlug' => $citySlug->getSlug()
                        ]
                    )
                ]
            );

            return $this->render(
                'CalderaCriticalmassSiteBundle:CityManagement:edit.html.twig',
                [
                    'city' => $city,
                    'form' => $form->createView(),
                    'hasErrors' => $hasErrors,
                    'country' => $region->getParent()->getName(),
                    'state' => $region->getName(),
                    'region' => $region
                ]
            );
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:CityManagement:edit.html.twig',
            [
                'city' => null,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'country' => $region->getParent()->getName(),
                'state' => $region->getName(),
                'region' => $region
            ]
        );
    }

    public function editAction(Request $request, $citySlug)
    {
        $city = $this->getCityBySlug($citySlug);

        $form = $this->createForm(new StandardCityType(), $city, array('action' => $this->generateUrl('caldera_criticalmass_desktop_city_edit', array('citySlug' => $city->getMainSlugString()))));

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $city, $form);
        } else {
            return $this->editGetAction($request, $city, $form);
        }
    }

    protected function editGetAction(Request $request, City $city, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:CityManagement:edit.html.twig',
            [
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => null,
                'country' => $city->getRegion()->getParent()->getName(),
                'state' => $city->getRegion()->getName(),
                'region' => $city->getRegion()
            ]
        );
    }

    protected function editPostAction(Request $request, City $city, Form $form)
    {
        $archiveCity = clone $city;
        $archiveCity->setArchiveUser($this->getUser());
        $archiveCity->setArchiveParent($city);

        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($city);
            $em->persist($archiveCity);
            $em->flush();

            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:CityManagement:edit.html.twig',
            [
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'country' => $city->getRegion()->getParent()->getName(),
                'state' => $city->getRegion()->getName(),
                'region' => $city->getRegion()
            ]
        );
    }

    public function createCityFlowAction(Request $request, $slug1, $slug2, $slug3)
    {
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->findOneBySlug($slug3);

        $city = new City();
        $city->setRegion($region);
        $city->setUser($this->getUser());

        $flow = $this->get('caldera.criticalmass.flow.create_city');
        $flow->bind($city);

        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $em = $this->getDoctrine()->getManager();

                $csg = new CitySlugGenerator($city);
                $citySlug = $csg->execute();
                $city->addSlug($citySlug);

                $em->persist($citySlug);
                $em->persist($city);
                $em->flush();

                $flow->reset();

                return $this->redirect($this->generateUrl($city));
            }
        }

        return $this->render('CalderaCriticalmassSiteBundle:CityManagement:create.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
            'city' => $city,
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region
        ));
    }
}
