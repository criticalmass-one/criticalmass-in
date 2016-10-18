<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Traits\RepositoryTrait;
use Caldera\Bundle\CriticalmassCoreBundle\Traits\UtilTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller
{
    use RepositoryTrait;
    use UtilTrait;

    protected function isLoggedIn(): bool
    {
        return $this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY');
    }
}