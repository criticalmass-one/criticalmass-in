<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CityCycleController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function listAction(City $city, ManagerRegistry $registry): Response
    {
        $cityCycleRepository = $registry->getRepository(CityCycle::class);

        return $this->render('CityCycle/list.html.twig', [
            'cycles' => $cityCycleRepository->findByCity($city),
            'city' => $city,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    public function listRidesAction(
        CityCycle $cityCycle,
        ManagerRegistry $registry
    ): Response {
        $rideRepository = $registry->getRepository(Ride::class);

        return $this->render('CityCycle/ride_list.html.twig', [
            'rideList' => $rideRepository->findByCycle($cityCycle),
            'cityCycle' => $cityCycle,
            'city' => $cityCycle->getCity(),
        ]);
    }
}
