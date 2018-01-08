<?php

namespace Criticalmass\Bundle\AppBundle\Controller\City;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Criticalmass\Bundle\AppBundle\Entity\Region;
use Criticalmass\Bundle\AppBundle\Form\Type\StandardCityType;
use Malenki\Slug;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class CityManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, UserInterface $user, string $slug1, string $slug2, string $slug3): Response
    {
        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->findOneBySlug($slug3);

        $city = new City();
        $city
            ->setRegion($region)
            ->setUser($this->getUser())
        ;

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
            return $this->addPostAction($request, $user, $city, $region, $form);
        } else {
            return $this->addGetAction($request, $user, $city, $region, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user, City $city, Region $region, Form $form)
    {
        return $this->render(
            'AppBundle:CityManagement:edit.html.twig',
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

    protected function addPostAction(Request $request, UserInterface $user, City $city, Region $region, Form $form)
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $citySlug = $this->createCitySlug($city);
            $city->addSlug($citySlug);

            $em->persist($citySlug);
            $em->persist($city);

            $em->flush();

            $hasErrors = false;

            $form = $this->createForm(
                StandardCityType::class,
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
                'AppBundle:CityManagement:edit.html.twig',
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
            'AppBundle:CityManagement:edit.html.twig',
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

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editAction(Request $request, UserInterface $user, string $citySlug): Response
    {
        $city = $this->getCityBySlug($citySlug);

        $form = $this->createForm(
            StandardCityType::class,
            $city,
            [
                'action' => $this->generateUrl(
                    'caldera_criticalmass_desktop_city_edit',
                    [
                        'citySlug' => $city->getMainSlugString()
                    ]
                )
            ]
        );

        if ('POST' == $request->getMethod()) {
            return $this->editPostAction($request, $user, $city, $form);
        } else {
            return $this->editGetAction($request, $user, $city, $form);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user, City $city, Form $form): Response
    {
        return $this->render(
            'AppBundle:CityManagement:edit.html.twig',
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

    protected function editPostAction(Request $request, UserInterface $user, City $city, Form $form): Response
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $city
                ->setUpdatedAt(new \DateTime())
                ->setUser($user)
            ;

            $this->getDoctrine()->getManager()->flush();

            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render(
            'AppBundle:CityManagement:edit.html.twig',
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

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function createCityFlowAction(Request $request, $slug1, $slug2, $slug3): Response
    {
        /** WTF is this? */
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        /**
         * @var Region $region
         */
        $region = $this->getRegionRepository()->findOneBySlug($slug3);

        $city = new City();
        $city
            ->setRegion($region)
            ->setUser($this->getUser())
        ;

        $flow = $this->get('caldera.criticalmass.flow.create_city');
        $flow->bind($city);

        $form = $flow->createForm();

        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData($form);

            if ($flow->nextStep()) {
                $form = $flow->createForm();
            } else {
                $em = $this->getDoctrine()->getManager();

                $citySlug = $this->createCitySlug($city);
                $city->addSlug($citySlug);

                $em->persist($citySlug);
                $em->persist($city);
                $em->flush();

                $flow->reset();

                return $this->redirectToObject($city);
            }
        }

        return $this->render('AppBundle:CityManagement:create.html.twig', array(
            'form' => $form->createView(),
            'flow' => $flow,
            'city' => $city,
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region
        ));
    }

    protected function createCitySlug(City $city): CitySlug
    {
        $slugString = new Slug($city->getCity());

        $citySlug = new CitySlug();
        $citySlug
            ->setCity($city)
            ->setSlug($slugString)
        ;

        return $citySlug;
    }
}
