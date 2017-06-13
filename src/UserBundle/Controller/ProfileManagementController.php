<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProfileManagementController extends Controller
{
    public function manageAction(): Response
    {
        return $this->render('UserBundle:ProfileManagement:manage.html.twig');
    }
}