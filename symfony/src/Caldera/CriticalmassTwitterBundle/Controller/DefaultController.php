<?php

namespace Caldera\CriticalmassTwitterBundle\Controller;

use Abraham\TwitterOAuth\TwitterOAuth;
use Caldera\CriticalmassTwitterBundle\Utility\TwitterGateway\TwitterGateway;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find(1118);

        $tg = new TwitterGateway($this->container->getParameter('twitter.consumer_key'), $this->container->getParameter('twitter.consumer_secret'), $this->container->getParameter('twitter.access_token'), $this->container->getParameter('twitter.access_token_secret'));
        $tg->fetchTweetsForRide($ride);

        return new Response('');
    }
}
