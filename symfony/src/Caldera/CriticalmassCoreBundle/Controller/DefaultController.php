<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Caldera\CriticalmassCoreBundle\Utility\StandardRideGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $srg = new StandardRideGenerator($this->getDoctrine(), 11, 2013);

        $srg->execute();

        return new Response();
    }
    }
}
