<?php

namespace Caldera\Bundle\CriticalmassRestBundle\Controller;

use FOS\RestBundle\View\View;

class CityController extends BaseController
{
    public function showAction($citySlug)
    {
        $city = $this->getDoctrine()->getRepository('CalderaBundle:City')->find(1);

        $view = View::create();
        $view
            ->setData($city)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
