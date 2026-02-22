<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class DevLoginController extends AbstractController
{
    #[Route('/dev/login/{email}', name: 'dev_login', priority: 200)]
    public function devLogin(
        string $email,
        ManagerRegistry $managerRegistry,
        LoginLinkHandlerInterface $loginLinkHandler,
        Request $request
    ): Response {
        if ('dev' !== $this->getParameter('kernel.environment')) {
            throw new NotFoundHttpException();
        }

        $user = $managerRegistry->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException(sprintf('User with email "%s" not found.', $email));
        }

        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

        return $this->redirect($loginLinkDetails->getUrl());
    }
}
