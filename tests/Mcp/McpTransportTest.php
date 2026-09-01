<?php declare(strict_types=1);

namespace Tests\Mcp;

/**
 * Transport-Ebene des /mcp-Endpunkts: Auth, HTTP-Methoden, JSON-RPC-Rahmen.
 */
final class McpTransportTest extends AbstractMcpTestCase
{
    public function testMissingTokenReturns401(): void
    {
        // Login-Token aus der TokenStorage entfernen (überlebt sonst wegen
        // disableReboot) → echt anonymer Request ohne Bearer-Token.
        $this->client->getContainer()->get('security.token_storage')->setToken(null);
        $this->client->getCookieJar()->clear();
        // Auth-Exception in eine 401-Response umwandeln lassen.
        $this->client->catchExceptions(true);

        $this->client->request('POST', '/mcp', [], [], ['CONTENT_TYPE' => 'application/json'], '{"jsonrpc":"2.0","id":1,"method":"ping"}');

        self::assertSame(401, $this->client->getResponse()->getStatusCode());
    }

    public function testGetReturnsMethodNotAllowed(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $this->client->request('GET', '/mcp', [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $token]);

        self::assertSame(405, $this->client->getResponse()->getStatusCode());
        self::assertSame('POST', $this->client->getResponse()->headers->get('Allow'));
    }

    public function testInvalidJsonReturnsParseError(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $this->client->request('POST', '/mcp', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], 'this is not json');

        $payload = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertSame(-32700, $payload['error']['code']);
        self::assertNull($payload['id']);
    }

    public function testBatchRequestIsRejected(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $this->client->request('POST', '/mcp', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], '[{"jsonrpc":"2.0","id":1,"method":"ping"}]');

        $payload = json_decode((string) $this->client->getResponse()->getContent(), true);
        self::assertSame(-32600, $payload['error']['code']);
    }

    public function testNotificationReturns202WithEmptyBody(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $this->client->request('POST', '/mcp', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            'CONTENT_TYPE' => 'application/json',
        ], '{"jsonrpc":"2.0","method":"notifications/initialized"}');

        self::assertSame(202, $this->client->getResponse()->getStatusCode());
        self::assertSame('', $this->client->getResponse()->getContent());
    }

    public function testInitializeOverHttp(): void
    {
        $token = $this->obtainAccessToken('ride:read');

        $response = $this->rpc($token, 'initialize', ['protocolVersion' => '2025-06-18']);

        self::assertSame('criticalmass.in', $response['result']['serverInfo']['name']);
    }
}
