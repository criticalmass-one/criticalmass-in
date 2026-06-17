<?php declare(strict_types=1);

namespace App\Controller\OAuth2;

use App\OAuth2\OAuthScope;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Discovery- und Registrierungs-Endpunkte, die MCP-Clients (ChatGPT-/Claude-
 * Connectoren) für ein automatisches Onboarding erwarten:
 *
 * - RFC 9728: OAuth 2.0 Protected Resource Metadata (der MCP-Resource-Server)
 * - RFC 8414: OAuth 2.0 Authorization Server Metadata
 * - RFC 7591: Dynamic Client Registration (öffentliche Clients mit PKCE)
 */
final class OAuth2DiscoveryController extends AbstractController
{
    public function __construct(
        private readonly ClientManagerInterface $clientManager,
    ) {
    }

    #[Route('/.well-known/oauth-protected-resource', name: 'oauth2_protected_resource_metadata', methods: ['GET'], priority: 500)]
    public function protectedResourceMetadata(Request $request, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $base = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'resource' => $base . '/mcp',
            'authorization_servers' => [$base],
            'scopes_supported' => OAuthScope::values(),
            'bearer_methods_supported' => ['header'],
        ]);
    }

    #[Route('/.well-known/oauth-authorization-server', name: 'oauth2_authorization_server_metadata', methods: ['GET'], priority: 500)]
    public function authorizationServerMetadata(Request $request, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $base = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'issuer' => $base,
            'authorization_endpoint' => $urlGenerator->generate('oauth2_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'token_endpoint' => $urlGenerator->generate('oauth2_token', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'registration_endpoint' => $urlGenerator->generate('oauth2_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'scopes_supported' => OAuthScope::values(),
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'code_challenge_methods_supported' => ['S256'],
            'token_endpoint_auth_methods_supported' => ['client_secret_basic', 'client_secret_post', 'none'],
        ]);
    }

    #[Route('/oauth2/register', name: 'oauth2_register', methods: ['POST'], priority: 500)]
    public function register(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = json_decode($request->getContent(), true) ?? [];

        $redirectUris = $payload['redirect_uris'] ?? null;

        if (!is_array($redirectUris) || [] === $redirectUris) {
            return $this->registrationError('invalid_redirect_uri', 'At least one redirect_uri is required.');
        }

        try {
            $redirectUriObjects = array_map(
                static fn (string $uri): RedirectUri => new RedirectUri($uri),
                array_values($redirectUris),
            );
        } catch (\Throwable $exception) {
            return $this->registrationError('invalid_redirect_uri', $exception->getMessage());
        }

        $clientName = is_string($payload['client_name'] ?? null) ? $payload['client_name'] : 'MCP Client';

        $requestedScopes = is_string($payload['scope'] ?? null)
            ? OAuthScope::filterKnown(explode(' ', trim($payload['scope'])))
            : [];
        $scopes = [] !== $requestedScopes ? $requestedScopes : OAuthScope::values();

        $identifier = 'mcp_' . bin2hex(random_bytes(12));

        // Öffentlicher Client (kein Secret) → PKCE erzwungen, User-Consent nötig.
        $client = new Client($clientName, $identifier, null);
        $client->setActive(true);
        $client->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $client->setRedirectUris(...$redirectUriObjects);
        $client->setScopes(...array_map(static fn (string $scope): Scope => new Scope($scope), $scopes));

        $this->clientManager->save($client);

        return new JsonResponse([
            'client_id' => $identifier,
            'client_id_issued_at' => time(),
            'client_name' => $clientName,
            'redirect_uris' => array_values($redirectUris),
            'grant_types' => ['authorization_code', 'refresh_token'],
            'response_types' => ['code'],
            'token_endpoint_auth_method' => 'none',
            'scope' => implode(' ', $scopes),
        ], JsonResponse::HTTP_CREATED);
    }

    private function registrationError(string $error, string $description): JsonResponse
    {
        return new JsonResponse([
            'error' => $error,
            'error_description' => $description,
        ], JsonResponse::HTTP_BAD_REQUEST);
    }
}
