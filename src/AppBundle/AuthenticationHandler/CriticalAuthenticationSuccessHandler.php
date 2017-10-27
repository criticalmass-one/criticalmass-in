<?php

namespace AppBundle\AuthenticationHandler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

class CriticalAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    protected function determineTargetUrl(Request $request): string
    {
        $targetUrl = $request->headers->get('Referer');

        if ($this->options['use_referer'] && !empty($targetUrl) && $targetUrl !== $this->httpUtils->generateUri($request, $this->options['login_path'])) {
            return $targetUrl;
        }

        return $this->options['default_target_path'];
    }
}
