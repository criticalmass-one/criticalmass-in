<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class ProfilePhotoController extends AbstractController
{
    public function editAction(Request $request)
    {
        return $this->render('CalderaCriticalmassSiteBundle:ProfilePhoto:edit.html.twig');
    }
}
