<?php declare(strict_types=1);

namespace Tests\OAuth2;

use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Verifiziert die OAuth2-Resource-Server-Firewall auf `^/mcp`:
 * ohne Token → 401, mit gültigem (aber rollen-unzureichendem) Token → 403.
 * Das beweist, dass die Bearer-Token-Validierung und die Autorisierung greifen.
 */
final class McpResourceServerTest extends WebTestCase
{
    public function testProtectedEndpointRejectsAnonymousRequest(): void
    {
        $client = static::createClient();
        $client->request('GET', '/mcp/health');

        self::assertSame(401, $client->getResponse()->getStatusCode());
    }

    public function testProtectedEndpointValidatesBearerTokenButEnforcesRole(): void
    {
        $client = static::createClient();
        // In-Memory-Token muss über beide Requests hinweg erhalten bleiben.
        $client->disableReboot();

        $this->registerClientCredentialsClient();
        $accessToken = $this->fetchAccessToken($client);

        $client->request('GET', '/mcp/health', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $accessToken,
        ]);

        // Token ist gültig (sonst 401), trägt aber kein ROLE_USER (nur ROLE_OAUTH2_*),
        // daher 403 durch die access_control-Regel auf ^/mcp.
        self::assertSame(403, $client->getResponse()->getStatusCode());
    }

    private function registerClientCredentialsClient(): void
    {
        /** @var ClientManagerInterface $clientManager */
        $clientManager = static::getContainer()->get(ClientManagerInterface::class);

        $oauthClient = new Client('MCP Resource PoC', 'mcp-resource-poc', 'poc-secret');
        $oauthClient->setActive(true);
        $oauthClient->setGrants(new Grant('client_credentials'));
        $oauthClient->setScopes(new Scope('ride:read'));
        $clientManager->save($oauthClient);
    }

    private function fetchAccessToken(KernelBrowser $client): string
    {
        $client->request('POST', '/token', [
            'grant_type' => 'client_credentials',
            'client_id' => 'mcp-resource-poc',
            'client_secret' => 'poc-secret',
            'scope' => 'ride:read',
        ]);

        self::assertSame(200, $client->getResponse()->getStatusCode());
        $payload = json_decode((string) $client->getResponse()->getContent(), true);
        self::assertIsArray($payload);

        return (string) $payload['access_token'];
    }
}
