<?php

namespace Caldera\CriticalmassCoreBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class LanguageListener
{
    private $session;

    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    public function setLocale(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        $request->setLocale($request->getPreferredLanguage(array('de')));
    }
}