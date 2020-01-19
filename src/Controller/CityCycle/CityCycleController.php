<?php declare(strict_types=1);

namespace App\Controller\CityCycle;

use App\Controller\AbstractController;
use App\Entity\City;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

class CityCycleController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City")
     */
    public function listAction(City $city): Response
    {
        return $this->render('CityCycle/list.html.twig', [
            'cycles' => $this->getCityCycleRepository()->findByCity($city),
            'city' => $city,
        ]);
    }
}
