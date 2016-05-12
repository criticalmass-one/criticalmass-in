<?php

namespace Caldera\Bundle\StatisticBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListener
{
    private $session;

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function listen(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }


        echo "FOO";
    }
}