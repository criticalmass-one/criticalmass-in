<?php

namespace Caldera\Bundle\CriticalmassRestBundle\Controller;

use Caldera\Bundle\CalderaBundle\Traits\RepositoryTrait;
use FOS\RestBundle\View\View;

class CityController extends BaseController
{
    use RepositoryTrait;

    public function showAction($citySlug)
    {
        $city = $this->getCityRepository();

        $view = View::create();
        $view
            ->setData($city)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
