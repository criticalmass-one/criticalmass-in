<?php declare(strict_types=1);

namespace Tests\Mcp;

use App\Entity\User;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\Client;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\RedirectUri;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * End-to-End-Test des MCP-Servers (/mcp): holt ein echtes User-Token über den
 * Authorization-Code-Flow und treibt damit das JSON-RPC-Protokoll
 * (initialize / tools/list / tools/call) inklusive Scope-Filterung.
 */
final class McpServerTest extends WebTestCase
{
    private const REDIRECT_URI = 'https://connector.example.org/callback';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->client->disableReboot();
        $this->client->catchExceptions(false);

        $em = $this->client->getContainer()->get('doctrine')->getManager();
        $user = new User();
        $user->setUsername('mcp-tester');
        $user->setEmail('mcp-tester@example.org');
        $user->setRoles(['ROLE_USER']);
        $user->setEnabled(true);
        $em->persist($user);
        $em->flush();

        $this->client->loginUser($user, 'user');

        /** @var ClientManagerInterface $clientManager */
        $clientManager = $this->client->getContainer()->get(ClientManagerInterface::class);
        $oauthClient = new Client('MCP Connector', 'mcp-connector', null);
        $oauthClient->setActive(true);
        $oauthClient->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $oauthClient->setRedirectUris(new RedirectUri(self::REDIRECT_URI));
        $oauthClient->setScopes(
            new Scope('ride:read'),
            new Scope('city:read'),
            new Scope('participation:write'),
            new Scope('ride:write'),
        );
        $clientManager->save($oauthClient);
    }

    public function testInitializeReturnsServerInfo(): void
    {
        $token = $this->obtainAccessToken('ride:read participation:write');

        $result = $this->rpc($token, 'initialize', [
            'protocolVersion' => '2025-06-18',
            'capabilities' => new \stdClass(),
            'clientInfo' => ['name' => 'pest', 'version' => '1'],
        ]);

        self::assertSame('2025-06-18', $result['result']['protocolVersion']);
        self::assertSame('criticalmass.in', $result['result']['serverInfo']['name']);
        self::assertArrayHasKey('tools', $result['result']['capabilities']);
    }

    public function testToolsListReflectsGrantedScopes(): void
    {
        $fullToken = $this->obtainAccessToken('ride:read participation:write');
        $names = $this->toolNames($this->rpc($fullToken, 'tools/list'));
        self::assertContains('list_rides', $names);
        self::assertContains('get_ride', $names);
        self::assertContains('set_participation', $names);

        $readToken = $this->obtainAccessToken('ride:read');
        $readNames = $this->toolNames($this->rpc($readToken, 'tools/list'));
        self::assertContains('list_rides', $readNames);
        self::assertNotContains('set_participation', $readNames);
        self::assertNotContains('create_ride', $readNames);

        $writeToken = $this->obtainAccessToken('ride:write');
        $writeNames = $this->toolNames($this->rpc($writeToken, 'tools/list'));
        self::assertContains('create_ride', $writeNames);
        self::assertContains('update_ride', $writeNames);
    }

    public function testToolCallEnforcesScope(): void
    {
        $readToken = $this->obtainAccessToken('ride:read');

        $result = $this->rpc($readToken, 'tools/call', [
            'name' => 'set_participation',
            'arguments' => ['citySlug' => 'hamburg', 'rideIdentifier' => '2026-01-01', 'status' => 'yes'],
        ]);

        self::assertTrue($result['result']['isError']);
        self::assertStringContainsString('Fehlender Scope', $result['result']['content'][0]['text']);
    }

    public function testToolCallReportsBusinessError(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->rpc($token, 'tools/call', [
            'name' => 'get_ride',
            'arguments' => ['citySlug' => 'gibt-es-nicht', 'rideIdentifier' => '2026-01-01'],
        ]);

        self::assertTrue($result['result']['isError']);
        self::assertStringContainsString('Kein Ride gefunden', $result['result']['content'][0]['text']);
    }

    public function testDataQueryToolReturnsJsonList(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->rpc($token, 'tools/call', [
            'name' => 'list_rides',
            'arguments' => ['size' => 5],
        ]);

        self::assertFalse($result['result']['isError']);
        $decoded = json_decode($result['result']['content'][0]['text'], true);
        self::assertIsArray($decoded);
    }

    public function testUnknownMethodReturnsJsonRpcError(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $result = $this->rpc($token, 'does/not/exist');

        self::assertSame(-32601, $result['error']['code']);
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function rpc(string $token, string $method, array $params = []): array
    {
        $body = ['jsonrpc' => '2.0', 'id' => 1, 'method' => $method];
        if ([] !== $params) {
            $body['params'] = $params;
        }

        $this->client->request('POST', '/mcp', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($body, JSON_THROW_ON_ERROR));

        self::assertResponseIsSuccessful();

        return json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<string, mixed> $listResponse
     *
     * @return list<string>
     */
    private function toolNames(array $listResponse): array
    {
        return array_map(
            static fn (array $tool): string => $tool['name'],
            $listResponse['result']['tools'],
        );
    }

    private function obtainAccessToken(string $scope): string
    {
        $verifier = 'mcp-poc-verifier-0123456789-0123456789-0123456789';
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        $authorizeUrl = '/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id' => 'mcp-connector',
            'redirect_uri' => self::REDIRECT_URI,
            'scope' => $scope,
            'state' => 'state-123',
            'code_challenge' => $challenge,
            'code_challenge_method' => 'S256',
        ]);

        $crawler = $this->client->request('GET', $authorizeUrl);
        $consentAuthorizeUrl = $crawler->filter('input[name="authorize_url"]')->attr('value');

        $this->client->request('POST', '/oauth2/consent', [
            'client_id' => 'mcp-connector',
            'authorize_url' => $consentAuthorizeUrl,
            'consent' => 'approve',
        ]);
        $this->client->followRedirect();

        $location = (string) $this->client->getResponse()->headers->get('Location');
        parse_str((string) parse_url($location, PHP_URL_QUERY), $callbackParams);

        $this->client->request('POST', '/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'mcp-connector',
            'redirect_uri' => self::REDIRECT_URI,
            'code' => $callbackParams['code'],
            'code_verifier' => $verifier,
        ]);

        $token = json_decode((string) $this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return (string) $token['access_token'];
    }
}
