<?php declare(strict_types=1);

namespace App\Mcp;

use App\Mcp\Tool\McpToolException;
use App\Mcp\Tool\McpToolInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

/**
 * Minimaler, spec-konformer MCP-Server (Model Context Protocol) über JSON-RPC 2.0.
 * Behandelt initialize / ping / tools/list / tools/call und delegiert an die
 * registrierten {@see McpToolInterface}-Werkzeuge. Scopes werden pro Tool anhand
 * der vom OAuth2-Token getragenen Berechtigungen erzwungen.
 */
final class McpServer
{
    /**
     * Vom Server unterstützte Protokollversionen (neueste zuerst).
     */
    private const SUPPORTED_PROTOCOL_VERSIONS = ['2025-06-18', '2025-03-26', '2024-11-05'];

    private const SERVER_NAME = 'criticalmass.in';
    private const SERVER_VERSION = '1.0.0';

    /**
     * @var array<string, McpToolInterface>
     */
    private array $tools = [];

    /**
     * @param iterable<McpToolInterface> $tools
     */
    public function __construct(
        #[AutowireIterator('app.mcp_tool')] iterable $tools,
    ) {
        foreach ($tools as $tool) {
            $this->tools[$tool->name()] = $tool;
        }
    }

    /**
     * Verarbeitet eine einzelne JSON-RPC-Nachricht.
     *
     * @param array<string, mixed> $message
     * @param list<string>         $grantedScopes
     *
     * @return array<string, mixed>|null Antwort-Envelope, oder null bei Notifications
     */
    public function handle(array $message, array $grantedScopes): ?array
    {
        $method = $message['method'] ?? null;

        if (!is_string($method)) {
            return $this->error($message['id'] ?? null, -32600, 'Invalid Request');
        }

        // Notifications (kein "id") werden bestätigt, aber nicht beantwortet.
        if (!array_key_exists('id', $message)) {
            return null;
        }

        $id = $message['id'];
        $params = \is_array($message['params'] ?? null) ? $message['params'] : [];

        return match ($method) {
            'initialize' => $this->result($id, $this->initialize($params)),
            'ping' => $this->result($id, new \stdClass()),
            'tools/list' => $this->result($id, ['tools' => $this->listTools($grantedScopes)]),
            'tools/call' => $this->result($id, $this->callTool($params, $grantedScopes)),
            default => $this->error($id, -32601, sprintf('Method not found: %s', $method)),
        };
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function initialize(array $params): array
    {
        $requested = $params['protocolVersion'] ?? null;
        $version = \is_string($requested) && \in_array($requested, self::SUPPORTED_PROTOCOL_VERSIONS, true)
            ? $requested
            : self::SUPPORTED_PROTOCOL_VERSIONS[0];

        return [
            'protocolVersion' => $version,
            'capabilities' => [
                'tools' => ['listChanged' => false],
            ],
            'serverInfo' => [
                'name' => self::SERVER_NAME,
                'version' => self::SERVER_VERSION,
            ],
        ];
    }

    /**
     * @param list<string> $grantedScopes
     *
     * @return list<array<string, mixed>>
     */
    private function listTools(array $grantedScopes): array
    {
        $tools = [];

        foreach ($this->tools as $tool) {
            if (!\in_array($tool->requiredScope()->value, $grantedScopes, true)) {
                continue;
            }

            $tools[] = [
                'name' => $tool->name(),
                'description' => $tool->description(),
                'inputSchema' => $tool->inputSchema(),
            ];
        }

        return $tools;
    }

    /**
     * @param array<string, mixed> $params
     * @param list<string>         $grantedScopes
     *
     * @return array<string, mixed>
     */
    private function callTool(array $params, array $grantedScopes): array
    {
        $name = $params['name'] ?? null;
        $tool = \is_string($name) ? ($this->tools[$name] ?? null) : null;

        if (null === $tool) {
            return $this->toolError(sprintf('Unbekanntes Werkzeug: %s', \is_string($name) ? $name : '(keines)'));
        }

        if (!\in_array($tool->requiredScope()->value, $grantedScopes, true)) {
            return $this->toolError(sprintf('Fehlender Scope: %s', $tool->requiredScope()->value));
        }

        $arguments = \is_array($params['arguments'] ?? null) ? $params['arguments'] : [];

        try {
            $text = $tool->call($arguments);
        } catch (McpToolException $exception) {
            return $this->toolError($exception->getMessage());
        }

        return [
            'content' => [['type' => 'text', 'text' => $text]],
            'isError' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function toolError(string $message): array
    {
        return [
            'content' => [['type' => 'text', 'text' => $message]],
            'isError' => true,
        ];
    }

    /**
     * @param array<string, mixed>|\stdClass $result
     *
     * @return array<string, mixed>
     */
    private function result(mixed $id, array|\stdClass $result): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function error(mixed $id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];
    }
}
