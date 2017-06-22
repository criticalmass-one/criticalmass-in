<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class BikerightController extends Controller
{
    public function viewAction(Request $request, UserInterface $user): Response
    {
        return $this->render(
            'UserBundle:BikeRight:view.html.twig',
            [
                'bikerightVoucher' => null,
            ]
        );
    }
}
