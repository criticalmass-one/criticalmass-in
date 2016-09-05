<?php

namespace Caldera\Bundle\CriticalmassRestBundle\Controller;

use Caldera\Bundle\CalderaBundle\Traits\RepositoryTrait;
use Caldera\Bundle\CriticalmassCoreBundle\Traits\UtilTrait;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

class CityController extends BaseController
{
    use RepositoryTrait;
    use UtilTrait;

    public function showAction(string $citySlug): Response
    {
        $city = $this->getCityRepository()->findOneByMainSlug($citySlug);

        $view = View::create();
        $view
            ->setData($city)
            ->setFormat('json')
            ->setStatusCode(200);

        return $this->handleView($view);
    }
}
