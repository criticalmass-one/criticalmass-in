<?php declare(strict_types=1);

namespace App\Controller\Oauth;

use App\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class InfoController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function infoAction(Request $request, UserInterface $user = null): Response
    {
        $json = [
            'username' => $user->getUsername(),
        ];

        return new JsonResponse($json);
    }
}