<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\OAuth2\OAuthScope;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * Ein über den MCP-Server (Streamable HTTP, /mcp) ausführbares Werkzeug.
 * Implementierungen werden automatisch registriert und nach dem vom
 * OAuth2-Token getragenen Scope autorisiert.
 */
#[AutoconfigureTag('app.mcp_tool')]
interface McpToolInterface
{
    public function name(): string;

    public function description(): string;

    /**
     * JSON-Schema der Tool-Argumente (MCP `inputSchema`).
     *
     * @return array<string, mixed>
     */
    public function inputSchema(): array;

    /**
     * Scope, den das aufrufende Token besitzen muss.
     */
    public function requiredScope(): OAuthScope;

    /**
     * Führt das Werkzeug aus und liefert das textuelle Ergebnis (häufig JSON).
     *
     * @param array<string, mixed> $arguments
     *
     * @throws McpToolException bei fachlichen Fehlern (dem Client als isError gemeldet)
     */
    public function call(array $arguments): string;
}
