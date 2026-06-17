<?php declare(strict_types=1);

namespace App\Controller\Mcp;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

/**
 * Minimaler, per OAuth2-Bearer-Token geschützter Endpunkt unter der mcp-Firewall.
 * Dient als Smoke-Test des Resource-Servers und als Health-Check; der eigentliche
 * MCP-Server (Streamable HTTP, /mcp) wird darauf aufgebaut.
 */
final class McpHealthController extends AbstractController
{
    #[Route('/mcp/health', name: 'mcp_health', methods: ['GET'])]
    public function health(#[CurrentUser] UserInterface $user): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'user' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
        ]);
    }
}
