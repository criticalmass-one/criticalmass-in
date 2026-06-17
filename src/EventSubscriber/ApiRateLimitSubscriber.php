<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Drosselt schreibende Zugriffe (POST/PUT/PATCH/DELETE) auf die anonyme
 * /api-Firewall pro Client-IP (#1392). Die echte IP setzt trusted_proxies voraus.
 */
final class ApiRateLimitSubscriber implements EventSubscriberInterface
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public function __construct(
        private readonly RateLimiterFactory $apiWriteLimiter,
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

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }

        if (!\in_array($request->getMethod(), self::WRITE_METHODS, true)) {
            return;
        }

        $limit = $this->apiWriteLimiter->create($request->getClientIp())->consume();

        if (false === $limit->isAccepted()) {
            throw new TooManyRequestsHttpException(
                max(0, $limit->getRetryAfter()->getTimestamp() - time()),
                'API rate limit exceeded.',
            );
        }
    }
}
