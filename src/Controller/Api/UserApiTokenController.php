<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserApiTokenController extends BaseController
{
    /**
     * Generate or retrieve an API token for the authenticated user.
     */
    #[Route('/api/user/token', name: 'caldera_criticalmass_rest_user_token', methods: ['POST'], priority: 200)]
    #[IsGranted('ROLE_USER')]
    #[OA\Tag(name: 'User')]
    #[OA\Response(response: 200, description: 'Returns the API token')]
    #[OA\Response(response: 401, description: 'Authentication required')]
    public function generateTokenAction(UserInterface $user): JsonResponse
    {
        /** @var User $user */
        if (!$user->getApiToken()) {
            $user->setApiToken(bin2hex(random_bytes(32)));

            $em = $this->managerRegistry->getManager();
            $em->flush();
        }

        return $this->createStandardResponse([
            'token' => $user->getApiToken(),
        ]);
    }
}
