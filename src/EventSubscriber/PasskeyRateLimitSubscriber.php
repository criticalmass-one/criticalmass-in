<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Drosselt die vier Passkey-Endpunkte pro Client-IP. Die echte IP setzt trusted_proxies
 * voraus.
 *
 * Ein Subscriber statt der Drosselung im Controller, weil die Controller vom Bundle
 * kommen und uns nicht gehören. Symfonys `login_throttling` an der Firewall wäre die
 * elegantere Variante, würde aber Magic Link und OAuth gleich mitdrosseln.
 *
 * Passkeys lassen sich nicht erraten, hier geht es also nicht um Brute Force, sondern um
 * Rechenlast: jede Anmeldung prüft eine Signatur. Die Grenzen sind entsprechend weit
 * gesetzt, damit sie hinter geteilten Adressen (Mobilfunk, Firmennetze) nicht im
 * Alltagsbetrieb zuschlagen.
 */
final class PasskeyRateLimitSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RateLimiterFactory $passkeyLimiter,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 16],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/passkey/')) {
            return;
        }

        $limit = $this->passkeyLimiter->create($request->getClientIp())->consume();

        if (false === $limit->isAccepted()) {
            throw new TooManyRequestsHttpException(
                max(0, $limit->getRetryAfter()->getTimestamp() - time()),
                'Zu viele Passkey-Anfragen. Bitte versuche es später erneut.',
            );
        }
    }
}
