<?php

namespace AppBundle\Controller\City;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
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
}
