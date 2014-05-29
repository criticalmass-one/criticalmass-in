<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

class UserController extends Controller
{
    public function userloginstatusAction()
    {
        $response = new Response();
        $response->setContent(json_encode(array(
            'login' => is_object($this->getUser()) ? 'true' : 'false'
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
