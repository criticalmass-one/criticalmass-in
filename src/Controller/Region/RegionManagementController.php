<?php declare(strict_types=1);

namespace App\Controller\Region;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Region;
use App\Form\Type\RegionType;
use App\Repository\RegionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class RegionManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/world/add', name: 'caldera_criticalmass_region_add_world', priority: 150)]
    #[Route('/world/{slug1}/add', name: 'caldera_criticalmass_region_add_1', priority: 150)]
    #[Route('/world/{slug1}/{slug2}/add', name: 'caldera_criticalmass_region_add_2', priority: 150)]
    #[Route('/world/{slug1}/{slug2}/{slug3}/add', name: 'caldera_criticalmass_region_add_3', priority: 150)]
    public function addAction(
        Request $request,
        RegionRepository $regionRepository,
        ObjectRouterInterface $objectRouter,
        ?string $slug1 = null,
        ?string $slug2 = null,
        ?string $slug3 = null,
    ): Response {
        $parentRegion = $this->resolveRegion($regionRepository, $slug1, $slug2, $slug3);

        if (!$parentRegion) {
            throw new NotFoundHttpException('Parent region not found.');
        }

        $region = new Region();
        $region->setParent($parentRegion);

        $form = $this->createForm(RegionType::class, $region, [
            'action' => $request->getRequestUri(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->managerRegistry->getManager();
            $em->persist($region);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Die Region wurde erfolgreich angelegt.');

            return $this->redirect($objectRouter->generate($region));
        }

        return $this->render('RegionManagement/edit.html.twig', [
            'form' => $form->createView(),
            'parentRegion' => $parentRegion,
            'region' => null,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/world/{slug1}/edit', name: 'caldera_criticalmass_region_edit_1', priority: 150)]
    #[Route('/world/{slug1}/{slug2}/edit', name: 'caldera_criticalmass_region_edit_2', priority: 150)]
    #[Route('/world/{slug1}/{slug2}/{slug3}/edit', name: 'caldera_criticalmass_region_edit_3', priority: 150)]
    public function editAction(
        Request $request,
        RegionRepository $regionRepository,
        ObjectRouterInterface $objectRouter,
        ?string $slug1 = null,
        ?string $slug2 = null,
        ?string $slug3 = null,
    ): Response {
        $region = $this->resolveRegion($regionRepository, $slug1, $slug2, $slug3);

        if (!$region) {
            throw new NotFoundHttpException('Region not found.');
        }

        $form = $this->createForm(RegionType::class, $region, [
            'action' => $request->getRequestUri(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->managerRegistry->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'Deine Änderungen wurden gespeichert.');

            return $this->redirect($objectRouter->generate($region));
        }

        return $this->render('RegionManagement/edit.html.twig', [
            'form' => $form->createView(),
            'parentRegion' => $region->getParent(),
            'region' => $region,
        ]);
    }

    private function resolveRegion(RegionRepository $regionRepository, ?string $slug1, ?string $slug2, ?string $slug3): ?Region
    {
        if ($slug3) {
            return $regionRepository->findOneBySlug($slug3);
        }

        if ($slug2) {
            return $regionRepository->findOneBySlug($slug2);
        }

        if ($slug1) {
            return $regionRepository->findOneBySlug($slug1);
        }

        return $regionRepository->find(1);
    }
}
