<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\CitySlug\Handler\CitySlugHandler;
use App\Criticalmass\OpenStreetMap\NominatimCityBridge\NominatimCityBridge;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Region;
use App\Event\City\CityCreatedEvent;
use App\Event\City\CityUpdatedEvent;
use App\Factory\City\CityFactoryInterface;
use App\Form\Type\CityType;
use App\Repository\RegionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CityManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function addAction(
        Request $request,
        ManagerRegistry $managerRegistry,
        UserInterface $user = null,
        NominatimCityBridge $nominatimCityBridge,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        CityFactoryInterface $cityFactory,
        string $slug1 = null,
        string $slug2 = null,
        string $slug3 = null,
        string $citySlug = null
    ): Response {
        $regionRepository = $managerRegistry->getRepository(Region::class);
        $region = $this->getRegion($nominatimCityBridge, $regionRepository, $slug3, $citySlug);

        if ($citySlug) {
            $city = $nominatimCityBridge->lookupCity($citySlug);
        } else {
            $cityFactory
                ->withUser($user)
                ->withRegion($region);

            $city = $cityFactory->build();
        }

        $form = $this->createForm(CityType::class, $city, [
            'action' => $this->generateUrl('caldera_criticalmass_city_add',
                $this->getRegionSlugParameterArray($region)),
        ]);

        if (Request::METHOD_POST == $request->getMethod()) {
            return $this->addPostAction($request, $user, $eventDispatcher, $objectRouter, $city, $region, $form);
        }

        return $this->addGetAction($request, $user, $eventDispatcher, $objectRouter, $city, $region, $form);
    }

    protected function addGetAction(
        Request $request,
        UserInterface $user = null,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        City $city,
        Region $region,
        FormInterface $form
    ) {
        return $this->render('CityManagement/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region,
        ]);
    }

    protected function addPostAction(
        Request $request,
        UserInterface $user = null,
        EventDispatcherInterface $eventDispatcher,
        ObjectRouterInterface $objectRouter,
        City $city,
        Region $region,
        FormInterface $form
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventDispatcher->dispatch(new CityCreatedEvent($city), CityCreatedEvent::NAME);

            $em = $this->managerRegistry->getManager();

            $citySlugs = CitySlugHandler::createSlugsForCity($city);

            foreach ($citySlugs as $citySlug) {
                $em->persist($citySlug);
            }

            $em->persist($city);

            $em->flush();

            $form = $this->createForm(CityType::class, $city, [
                'action' => $objectRouter->generate($city, 'caldera_criticalmass_city_edit'),
            ]);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($city));
        }

        return $this->render('CityManagement/edit.html.twig', [
            'city' => null,
            'form' => $form->createView(),
            'country' => $region->getParent()->getName(),
            'state' => $region->getName(),
            'region' => $region,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    public function editAction(
        Request $request,
        UserInterface $user = null,
        EventDispatcherInterface $eventDispatcher,
        City $city,
        ObjectRouterInterface $objectRouter
    ): Response {
        $form = $this->createForm(CityType::class, $city, [
            'action' => $objectRouter->generate($city, 'caldera_criticalmass_city_edit'),
        ]);

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->editPostAction($request, $user, $eventDispatcher, $city, $form, $objectRouter);
        }

        return $this->editGetAction($request, $user, $eventDispatcher, $city, $form, $objectRouter);
    }

    protected function editGetAction(
        Request $request,
        UserInterface $user = null,
        EventDispatcherInterface $eventDispatcher,
        City $city,
        FormInterface $form,
        ObjectRouterInterface $objectRouter
    ): Response {
        return $this->render('CityManagement/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
            'country' => $city->getRegion()->getParent()->getName(),
            'state' => $city->getRegion()->getName(),
            'region' => $city->getRegion(),
        ]);
    }

    protected function editPostAction(
        Request $request,
        UserInterface $user = null,
        EventDispatcherInterface $eventDispatcher,
        City $city,
        FormInterface $form,
        ObjectRouterInterface $objectRouter
    ): Response {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city
                ->setUpdatedAt(new \DateTime())
                ->setUser($user)
            ;

            $this->managerRegistry->getManager()->flush();

            $eventDispatcher->dispatch(new CityUpdatedEvent($city), CityUpdatedEvent::NAME);

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($city));
        }

        return $this->render('CityManagement/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
            'country' => $city->getRegion()->getParent()->getName(),
            'state' => $city->getRegion()->getName(),
            'region' => $city->getRegion(),
        ]);
    }

    protected function getRegion(
        NominatimCityBridge $nominatimCityBridge,
        RegionRepository $regionRepository,
        string $regionSlug = null,
        string $citySlug = null
    ): ?Region {
        if ($regionSlug) {
            return $regionRepository->findOneBySlug($regionSlug);
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
