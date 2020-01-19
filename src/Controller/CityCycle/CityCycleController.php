<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Entity\City;
use App\Entity\CityCycle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;

class CityCycleController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function listAction(City $city, RegistryInterface $registry): Response
    {
        $cityCycleRepository = $registry->getRepository(CityCycle::class);

        return $this->render('CityCycle/list.html.twig', [
            'cycles' => $cityCycleRepository->findByCity($city),
            'city' => $city,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("cityCycle", class="App:CityCycle")
     */
    public function listRidesAction(CityCycle $cityCycle): Response
    {
        return $this->render('CityCycle/ride_list.html.twig', [
            'rides' => $this->getRideRepository()->findByCycle($cityCycle),
            'cityCycle' => $cityCycle,
            'city' => $cityCycle->getCity(),
        ]);
    }
}
