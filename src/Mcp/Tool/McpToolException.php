<?php declare(strict_types=1);

namespace App\Mcp\Tool;

/**
 * Fachlicher Fehler bei der Tool-Ausführung. Wird dem MCP-Client als
 * `isError`-Ergebnis (nicht als JSON-RPC-Protokollfehler) zurückgegeben.
 */
final class McpToolException extends \RuntimeException
{
}
