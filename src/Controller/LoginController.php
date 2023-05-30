<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\LoginType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class LoginController extends AbstractController
{
    public function __construct(
        protected NotifierInterface $notifier,
        protected LoginLinkHandlerInterface $loginLinkHandler,
        protected UserRepository $userRepository,
        protected ManagerRegistry $managerRegistry
    )
    {

    }

    #[Route('/login', name: 'login')]
    public function requestLoginLink(Request $request): Response
    {
        $loginForm = $this->createForm(LoginType::class);

        $loginForm->handleRequest($request);

        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $email = $loginForm->getData()['email'];

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $user = $this->createNewUser($email);
            }

            $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Dein persönlicher Login-Link für criticalmass.in!'
            );

            $notification->content(sprintf("Bitte klicke auf den folgenden Button, um dich bei criticalmass.in einzuloggen. Dein persönlicher Link zum Einloggen ist %s lang gültig.", static::createLoginLinkDuration($loginLinkDetails)));

            $recipient = new Recipient($user->getEmail());

            $this->notifier->send($notification, $recipient);

            return $this->render('login/login_link_sent.html.twig', [
                'login_form' => $loginForm->createView(),
            ]);
        }

        return $this->render('login/login.html.twig', [
            'login_form' => $loginForm->createView(),
        ]);
    }

    public function createNewUser(string $email): User
    {
        $user = new User();
        $user->setEmail($email);

        $em = $this->managerRegistry->getManager();

        $em->persist($user);
        $em->flush();

        return $user;
    }

    #[Route('/login_check', name: 'login_check')]
    public function check(): never
    {
        throw new \LogicException('This code should never be reached');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): never
    {
        throw new \LogicException('This code should never be reached');
    }

    protected static function createLoginLinkDuration(LoginLinkDetails $loginLinkDetails): string
    {
        $duration = $loginLinkDetails->getExpiresAt()->getTimestamp() - time();

        $durationString = floor($duration / 60).' Minute'.($duration > 60 ? 'n' : '');

        if (($hours = $duration / 3600) >= 1) {
            $durationString = floor($hours).' Stunde'.($hours >= 2 ? 'n' : '');
        }

        return $durationString;
    }
}
