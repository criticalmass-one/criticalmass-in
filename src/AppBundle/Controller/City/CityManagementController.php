<?php

namespace AppBundle\Controller\City;

use AppBundle\Criticalmass\OpenStreetMap\NominatimCityBridge\NominatimCityBridge;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\City;
use AppBundle\Entity\CitySlug;
use AppBundle\Entity\Region;
use AppBundle\Form\Type\StandardCityType;
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
    public function addAction(
        Request $request,
        UserInterface $user = null,
        NominatimCityBridge $nominatimCityBridge,
        string $slug1 = null,
        string $slug2 = null,
        string $slug3 = null,
        string $citySlug = null
    ): Response {
        $region = $this->getRegion($nominatimCityBridge, $slug3, $citySlug);

        if ($citySlug) {
            $city = $nominatimCityBridge->lookupCity($citySlug);
        } else {
            $city = new City();
            $city->setRegion($region);
        }

        $city->setUser($this->getUser());

        $form = $this->createForm(StandardCityType::class, $city, [
            'action' => $this->generateUrl('caldera_criticalmass_city_add', $this->getRegionSlugParameterArray($region)),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->addPostAction($request, $user, $city, $region, $form);
        } else {
            return $this->addGetAction($request, $user, $city, $region, $form);
        }
    }

    protected function addGetAction(Request $request, UserInterface $user, City $city, Region $region, Form $form)
    {
        return $this->render('AppBundle:CityManagement:edit.html.twig', [
            'city' => null,
            'form' => $form->createView(),
            'hasErrors' => null,
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region,
        ]);
    }

    protected function addPostAction(Request $request, UserInterface $user, City $city, Region $region, Form $form): Response
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

            $form = $this->createForm(StandardCityType::class, $city, [
                'action' => $this->generateUrl('caldera_criticalmass_city_edit', [
                    'citySlug' => $citySlug->getSlug(),
                ])
            ]);

            return $this->render('AppBundle:CityManagement:edit.html.twig', [
                'city' => $city,
                'form' => $form->createView(),
                'hasErrors' => $hasErrors,
                'country' => $region->getParent()->getName(),
                'state' => $region->getName(),
                'region' => $region,
            ]);
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render('AppBundle:CityManagement:edit.html.twig', [
            'city' => null,
            'form' => $form->createView(),
            'hasErrors' => $hasErrors,
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="AppBundle:City")
     */
    public function editAction(Request $request, UserInterface $user = null, City $city): Response
    {
        $form = $this->createForm(StandardCityType::class, $city, [
            'action' => $this->generateUrl('caldera_criticalmass_city_edit', [
                'citySlug' => $city->getMainSlugString()
            ])
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->editPostAction($request, $user, $city, $form);
        } else {
            return $this->editGetAction($request, $user, $city, $form);
        }
    }

    protected function editGetAction(Request $request, UserInterface $user, City $city, Form $form): Response
    {
        return $this->render('AppBundle:CityManagement:edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
            'hasErrors' => null,
            'country' => $city->getRegion()->getParent()->getName(),
            'state' => $city->getRegion()->getName(),
            'region' => $city->getRegion(),
        ]);
    }

    protected function editPostAction(Request $request, UserInterface $user, City $city, Form $form): Response
    {
        $form->handleRequest($request);

        $hasErrors = null;

        if ($form->isValid()) {
            $city
                ->setUpdatedAt(new \DateTime())
                ->setUser($user);

            $this->getDoctrine()->getManager()->flush();

            $hasErrors = false;
        } elseif ($form->isSubmitted()) {
            $hasErrors = true;
        }

        return $this->render('AppBundle:CityManagement:edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
            'hasErrors' => $hasErrors,
            'country' => $city->getRegion()->getParent()->getName(),
            'state' => $city->getRegion()->getName(),
            'region' => $city->getRegion(),
        ]);
    }

    protected function createCitySlug(City $city): CitySlug
    {
        $slugString = new Slug($city->getCity());

        $citySlug = new CitySlug();
        $citySlug
            ->setCity($city)
            ->setSlug($slugString);

        return $citySlug;
    }

    protected function getRegion(NominatimCityBridge $nominatimCityBridge, string $regionSlug = null, string $citySlug = null): ?Region
    {
        if ($regionSlug) {
            return $this->getRegionRepository()->findOneBySlug($regionSlug);
        }

        if ($citySlug) {
            $city = $nominatimCityBridge->lookupCity($citySlug);

            if ($city) {
                return $city->getRegion();
            }
        }

        return null;
    }

    protected function getRegionSlugParameterArray(Region $region): array
    {
        return [
            'slug1' => $region->getParent()->getParent()->getSlug(),
            'slug2' => $region->getParent()->getSlug(),
            'slug3' => $region->getSlug(),
        ];
    }
}
