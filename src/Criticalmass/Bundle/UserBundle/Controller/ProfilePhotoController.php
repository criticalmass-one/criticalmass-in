<?php

namespace Criticalmass\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ProfilePhotoController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadAction(): Response
    {
        return $this->render('UserBundle:ProfilePhoto:upload.html.twig');
    }
}
