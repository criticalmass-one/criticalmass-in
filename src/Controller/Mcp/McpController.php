<?php declare(strict_types=1);

namespace App\Controller\Mcp;

use App\Mcp\McpServer;
use League\Bundle\OAuth2ServerBundle\Security\Authentication\Token\OAuth2Token;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Streamable-HTTP-Endpunkt des MCP-Servers. Per OAuth2-Bearer-Token (mcp-Firewall)
 * geschützt; die Scopes des Tokens steuern die verfügbaren Werkzeuge.
 *
 * Route-Priority 500, da `/mcp` sonst von der Catch-all-Route `/{citySlug}`
 * (Priority 100) verschluckt würde.
 */
final class McpController extends AbstractController
{
    public function __construct(
        private readonly McpServer $mcpServer,
        private readonly Security $security,
    ) {
    }

    #[Route('/mcp', name: 'mcp_server', methods: ['POST'], priority: 500)]
    public function handle(Request $request): Response
    {
        try {
            /** @var mixed $message */
            $message = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return new JsonResponse($this->protocolError(-32700, 'Parse error'));
        }

        if (!\is_array($message) || array_is_list($message)) {
            // Einzelne Objekt-Nachrichten; JSON-RPC-Batches werden nicht unterstützt.
            return new JsonResponse($this->protocolError(-32600, 'Invalid Request'));
        }

        $response = $this->mcpServer->handle($message, $this->grantedScopes());

        if (null === $response) {
            // Notification → kein Body.
            return new Response('', Response::HTTP_ACCEPTED);
        }

        return new JsonResponse($response);
    }

    #[Route('/mcp', name: 'mcp_server_no_stream', methods: ['GET'], priority: 500)]
    public function noServerSentEvents(): Response
    {
        // Server-initiierte SSE-Streams werden nicht angeboten.
        return new Response('', Response::HTTP_METHOD_NOT_ALLOWED, ['Allow' => 'POST']);
    }

    /**
     * @return list<string>
     */
    private function grantedScopes(): array
    {
        $token = $this->security->getToken();

        return $token instanceof OAuth2Token ? $token->getScopes() : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function protocolError(int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => null,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];
    }
}
