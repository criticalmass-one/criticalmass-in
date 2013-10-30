<?php

namespace Caldera\CriticalmassCoreBundle\Controller;

use Caldera\CriticalmassCoreBundle\Utility\RideCompiler;
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

    public function index2Action()
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->findOneById(67);

        $rc = new RideCompiler($ride, $this->getDoctrine());
        $rc->execute();

        return new Response();
    }
}
