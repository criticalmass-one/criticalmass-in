<?php declare(strict_types=1);

namespace App\Controller\OAuth2;

use League\Bundle\OAuth2ServerBundle\Controller\AuthorizationController;
use League\Bundle\OAuth2ServerBundle\Controller\TokenController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Dünne App-Wrapper für die OAuth2-Endpunkte des league/oauth2-server-bundle.
 *
 * Die Bundle-Default-Routen besitzen Priority 0 und würden von den Catch-all-
 * Routen der App (`/{citySlug}`, Priority 100, und `/{citySlug}/{rideIdentifier}`)
 * verschluckt – `/token` bzw. `/authorize` würden als City-Slug interpretiert.
 * Route-Priority lässt sich in Symfony nur per Attribut setzen, daher hier mit
 * hoher Priority deklariert (analog zu /login = Priority 200) und an die
 * Bundle-Controller delegiert.
 */
final class OAuth2Controller
{
    public function __construct(
        #[Autowire(service: 'league.oauth2_server.controller.authorization')]
        private readonly AuthorizationController $authorizationController,
        #[Autowire(service: 'league.oauth2_server.controller.token')]
        private readonly TokenController $tokenController,
    ) {
    }

    #[Route('/authorize', name: 'oauth2_authorize', priority: 500)]
    public function authorize(Request $request): Response
    {
        return $this->authorizationController->indexAction($request);
    }

    #[Route('/token', name: 'oauth2_token', methods: ['POST'], priority: 500)]
    public function token(Request $request): Response
    {
        return $this->tokenController->indexAction($request);
    }
}
