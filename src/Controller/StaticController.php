<?php

namespace AppBundle\Controller;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticController extends AbstractController
{
    public function displayStaticContentAction(Request $request, string $slug): Response
    {
        try {
            return $this->render('AppBundle:Static:' . $slug . '.html.twig');
        } catch (InvalidArgumentException $e) {
            throw $this->createNotFoundException('There is no content for slug "' . $slug . '"');
        }
    }
}
