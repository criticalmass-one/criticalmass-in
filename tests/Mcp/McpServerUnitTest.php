<?php declare(strict_types=1);

namespace Tests\Mcp;

use App\Mcp\McpServer;
use App\Mcp\Tool\McpToolException;
use App\Mcp\Tool\McpToolInterface;
use App\OAuth2\OAuthScope;
use PHPUnit\Framework\TestCase;

/**
 * Reine Unit-Tests des JSON-RPC-Dispatchers (kein Kernel, kein DB). Nutzt
 * konfigurierbare Fake-Tools, um jeden Pfad von McpServer::handle() abzudecken.
 */
final class McpServerUnitTest extends TestCase
{
    private function server(McpToolInterface ...$tools): McpServer
    {
        return new McpServer($tools);
    }

    private function tool(string $name, OAuthScope $scope, ?callable $call = null): McpToolInterface
    {
        return new class($name, $scope, $call) implements McpToolInterface {
            /** @var callable|null */
            private $call;

            public function __construct(
                private readonly string $toolName,
                private readonly OAuthScope $scope,
                ?callable $call,
            ) {
                $this->call = $call;
            }

            public function name(): string
            {
                return $this->toolName;
            }

            public function description(): string
            {
                return 'Fake tool ' . $this->toolName;
            }

            public function inputSchema(): array
            {
                return ['type' => 'object', 'properties' => []];
            }

            public function requiredScope(): OAuthScope
            {
                return $this->scope;
            }

            public function call(array $arguments): string
            {
                return null !== $this->call ? ($this->call)($arguments) : 'result:' . $this->toolName;
            }
        };
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function request(int|string|null $id, string $method, array $params = []): array
    {
        $message = ['jsonrpc' => '2.0', 'method' => $method];
        if (null !== $id) {
            $message['id'] = $id;
        }
        if ([] !== $params) {
            $message['params'] = $params;
        }

        return $message;
    }

    public function testInitializeEchoesSupportedProtocolVersion(): void
    {
        $response = $this->server()->handle($this->request(1, 'initialize', ['protocolVersion' => '2025-03-26']), []);

        self::assertSame('2.0', $response['jsonrpc']);
        self::assertSame(1, $response['id']);
        self::assertSame('2025-03-26', $response['result']['protocolVersion']);
        self::assertSame('criticalmass.in', $response['result']['serverInfo']['name']);
        self::assertArrayHasKey('tools', $response['result']['capabilities']);
    }

    public function testInitializeFallsBackToLatestForUnknownVersion(): void
    {
        $response = $this->server()->handle($this->request(1, 'initialize', ['protocolVersion' => '1999-01-01']), []);

        self::assertSame('2025-06-18', $response['result']['protocolVersion']);
    }

    public function testInitializeWithoutVersionUsesLatest(): void
    {
        $response = $this->server()->handle($this->request(1, 'initialize'), []);

        self::assertSame('2025-06-18', $response['result']['protocolVersion']);
    }

    public function testPingReturnsEmptyResult(): void
    {
        $response = $this->server()->handle($this->request(7, 'ping'), []);

        self::assertSame(7, $response['id']);
        self::assertEquals(new \stdClass(), $response['result']);
    }

    public function testToolsListFiltersByGrantedScope(): void
    {
        $server = $this->server(
            $this->tool('reader', OAuthScope::RideRead),
            $this->tool('writer', OAuthScope::RideWrite),
        );

        $response = $server->handle($this->request(2, 'tools/list'), ['ride:read']);
        $names = array_column($response['result']['tools'], 'name');

        self::assertContains('reader', $names);
        self::assertNotContains('writer', $names);
    }

    public function testToolsListExposesNameDescriptionAndSchema(): void
    {
        $server = $this->server($this->tool('reader', OAuthScope::RideRead));

        $tools = $server->handle($this->request(2, 'tools/list'), ['ride:read'])['result']['tools'];

        self::assertCount(1, $tools);
        self::assertSame('reader', $tools[0]['name']);
        self::assertArrayHasKey('description', $tools[0]);
        self::assertSame('object', $tools[0]['inputSchema']['type']);
    }

    public function testToolsListEmptyWithoutMatchingScope(): void
    {
        $server = $this->server($this->tool('writer', OAuthScope::RideWrite));

        $tools = $server->handle($this->request(2, 'tools/list'), ['ride:read'])['result']['tools'];

        self::assertSame([], $tools);
    }

    public function testToolCallSucceeds(): void
    {
        $server = $this->server($this->tool('reader', OAuthScope::RideRead, fn (array $a): string => 'echo:' . ($a['x'] ?? '')));

        $response = $server->handle($this->request(3, 'tools/call', [
            'name' => 'reader',
            'arguments' => ['x' => '42'],
        ]), ['ride:read']);

        self::assertFalse($response['result']['isError']);
        self::assertSame('text', $response['result']['content'][0]['type']);
        self::assertSame('echo:42', $response['result']['content'][0]['text']);
    }

    public function testToolCallUnknownToolIsError(): void
    {
        $response = $this->server()->handle($this->request(3, 'tools/call', ['name' => 'nope']), ['ride:read']);

        self::assertTrue($response['result']['isError']);
        self::assertStringContainsString('Unbekanntes Werkzeug', $response['result']['content'][0]['text']);
    }

    public function testToolCallMissingScopeIsError(): void
    {
        $server = $this->server($this->tool('writer', OAuthScope::RideWrite));

        $response = $server->handle($this->request(3, 'tools/call', ['name' => 'writer']), ['ride:read']);

        self::assertTrue($response['result']['isError']);
        self::assertStringContainsString('Fehlender Scope', $response['result']['content'][0]['text']);
    }

    public function testToolCallBusinessExceptionIsError(): void
    {
        $server = $this->server($this->tool('reader', OAuthScope::RideRead, function (): string {
            throw new McpToolException('kaputt');
        }));

        $response = $server->handle($this->request(3, 'tools/call', ['name' => 'reader']), ['ride:read']);

        self::assertTrue($response['result']['isError']);
        self::assertSame('kaputt', $response['result']['content'][0]['text']);
    }

    public function testNotificationReturnsNull(): void
    {
        $response = $this->server()->handle($this->request(null, 'notifications/initialized'), []);

        self::assertNull($response);
    }

    public function testMissingMethodIsInvalidRequest(): void
    {
        $response = $this->server()->handle(['jsonrpc' => '2.0', 'id' => 5], []);

        self::assertSame(-32600, $response['error']['code']);
    }

    public function testUnknownMethodReturnsMethodNotFound(): void
    {
        $response = $this->server()->handle($this->request(9, 'foo/bar'), []);

        self::assertSame(-32601, $response['error']['code']);
        self::assertSame(9, $response['id']);
    }

    public function testStringIdIsEchoed(): void
    {
        $response = $this->server()->handle($this->request('abc', 'ping'), []);

        self::assertSame('abc', $response['id']);
    }
}
