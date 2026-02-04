<?php declare(strict_types=1);

namespace App\EventListener;

use League\Bundle\OAuth2ServerBundle\Event\AuthorizationRequestResolveEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

#[AsEventListener(event: 'league.oauth2_server.event.authorization_request_resolve')]
final class OAuth2AuthorizationListener
{
    public function __construct(
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function __invoke(AuthorizationRequestResolveEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $session = $request->getSession();
        $consentKey = sprintf('oauth2_consent_%s', $event->getClient()->getIdentifier());

        if ($session->has($consentKey)) {
            $approved = $session->get($consentKey);
            $session->remove($consentKey);
            $event->resolveAuthorization((bool) $approved);

            return;
        }

        $html = $this->twig->render('OAuth2/consent.html.twig', [
            'client' => $event->getClient(),
            'scopes' => $event->getScopes(),
            'authorize_url' => $request->getUri(),
        ]);

        $event->setResponse(new Response($html));
    }
}
