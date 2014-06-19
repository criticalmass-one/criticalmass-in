<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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


    public function getpingtokenAction()
    {
        $response = new Response();
        $response->setContent(json_encode(array(
            'pingToken' => $this->getUser()->getPingToken()
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function getcolorsAction()
    {
        $response = new Response();
        $response->setContent(json_encode(array(
            'red' => $this->getUser()->getColorRed(),
            'green' => $this->getUser()->getColorGreen(),
            'blue' => $this->getUser()->getColorBlue()
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function setcolorsAction(Request $request)
    {
        $colors['red'] = $request->query->get('red');
        $colors['green'] = $request->query->get('green');
        $colors['blue'] = $request->query->get('blue');

        foreach ($colors as $color)
        {
            if (!($color >= 0 && $color < 256) || !isset($color))
            {
                throw $this->createNotFoundException('Please make sure to set the right range for each color value.');
            }
        }

        $user = $this->getUser();

        $user->setColorRed($colors['red']);
        $user->setColorGreen($colors['green']);
        $user->setColorBlue($colors['blue']);

        $this->get('fos_user.user_manager')->updateUser($user);

        return new Response();
    }

    public function switchcityAction($citySlug)
    {
        $citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:CitySlug')->findOneBySlug($citySlug);

        $user = $this->getUser();

        $user->setCurrentCity($citySlug->getCity());

        $this->get('fos_user.user_manager')->updateUser($user);

        return new Response($citySlug->getCity()->getId());
    }
}
