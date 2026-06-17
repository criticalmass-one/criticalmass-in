<?php declare(strict_types=1);

namespace App\Controller\City;

use App\Controller\AbstractController;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Form\Type\CityHeaderImageType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CityHeaderImageController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/{citySlug}/headerimage', name: 'caldera_criticalmass_city_headerimage', priority: 100)]
    public function uploadAction(
        Request $request,
        ObjectRouterInterface $objectRouter,
        City $city
    ): Response {
        $form = $this->createForm(CityHeaderImageType::class, $city, [
            'action' => $objectRouter->generate($city, 'caldera_criticalmass_city_headerimage'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $city->setUpdatedAt(new \DateTime());

            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Das Header-Bild wurde erfolgreich aktualisiert.');

            return $this->redirect($objectRouter->generate($city));
        }

        return $this->render('CityHeaderImage/upload.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }
}
