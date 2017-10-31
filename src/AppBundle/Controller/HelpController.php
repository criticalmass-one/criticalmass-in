<?php

namespace AppBundle\Controller;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelpController extends AbstractController
{
    public function indexAction(Request $request): Response
    {
        return $this->render('AppBundle:Help:help.html.twig');
    }
}
