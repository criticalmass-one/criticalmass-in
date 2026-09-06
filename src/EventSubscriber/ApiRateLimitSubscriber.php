<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

/**
 * Drosselt schreibende Zugriffe (POST/PUT/PATCH/DELETE) auf die anonyme
 * /api-Firewall (#1392). Die echte IP setzt trusted_proxies voraus.
 *
 * Wer sich mit dem vereinbarten Token ausweist, bekommt ein eigenes, weites
 * Kontingent. Das ist kein Nachlass, sondern eine Berichtigung: Der naechtliche
 * Aktivitaetslauf schickt **eine Anfrage je Stadt** an
 * POST /api/city/{slug}/activity, bei ueber 700 Staedten gegen ein Kontingent
 * von 120 je 15 Minuten. Er wiederholt bei 429, wodurch sich der Lauf auf
 * anderthalb Stunden streckte, rund 50 Staedte je Nacht ausfielen und seit dem
 * 25. Juli 17.159 Fehlermeldungen aufliefen — etwa 730 pro Nacht.
 *
 * Die Bremse fuer alle anderen bleibt unveraendert; sie ist das Einzige, was
 * vor dieser Firewall steht.
 */
final class ApiRateLimitSubscriber implements EventSubscriberInterface
{
    private const WRITE_METHODS = ['POST', 'PUT', 'PATCH', 'DELETE'];

    public const TOKEN_HEADER = 'X-Criticalmass-Token';

    public function __construct(
        private readonly RateLimiterFactory $apiWriteLimiter,
        private readonly RateLimiterFactory $apiWriteTrustedLimiter,
        private readonly string $eigenerDienstToken = '',
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

        $limit = $this->istEigenerDienst($request)
            ? $this->apiWriteTrustedLimiter->create('eigener-dienst')->consume()
            : $this->apiWriteLimiter->create($request->getClientIp())->consume();

        if (false === $limit->isAccepted()) {
            throw new TooManyRequestsHttpException(
                max(0, $limit->getRetryAfter()->getTimestamp() - time()),
                'API rate limit exceeded.',
            );
        }
    }

    /**
     * Ein leer gelassenes Token gilt nie als Uebereinstimmung — sonst wuerde
     * eine vergessene Konfiguration jeden Absender ohne Kopfzeile
     * durchwinken.
     */
    private function istEigenerDienst(Request $request): bool
    {
        if ('' === $this->eigenerDienstToken) {
            return false;
        }

        $mitgeschickt = $request->headers->get(self::TOKEN_HEADER, '');

        if ('' === $mitgeschickt) {
            return false;
        }

        return hash_equals($this->eigenerDienstToken, $mitgeschickt);
    }
}
