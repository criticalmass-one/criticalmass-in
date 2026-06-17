<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onResponse',
        ];
    }

    /**
     * Content-Security-Policy zunächst nur als Report-Only ausrollen: bricht
     * nichts, sammelt aber Verstöße (z. B. Inline-Skripte), bevor auf eine
     * erzwingende Policy umgestellt wird. `script-src` bewusst ohne
     * `unsafe-inline`, damit Inline-Skripte sichtbar werden.
     */
    private const CONTENT_SECURITY_POLICY = "default-src 'self'; "
        . "base-uri 'self'; "
        . "object-src 'none'; "
        . "frame-ancestors 'self'; "
        . "img-src 'self' data: https:; "
        . "font-src 'self' data: https:; "
        . "style-src 'self' 'unsafe-inline'; "
        . "script-src 'self'; "
        . "connect-src 'self' https:";

    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        if (!$response->headers->has('Content-Security-Policy-Report-Only')
            && !$response->headers->has('Content-Security-Policy')) {
            $response->headers->set('Content-Security-Policy-Report-Only', self::CONTENT_SECURITY_POLICY);
        }

        // HSTS nur über HTTPS senden (Browser ignorieren es sonst ohnehin).
        if ($event->getRequest()->isSecure() && !$response->headers->has('Strict-Transport-Security')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }
    }
}
