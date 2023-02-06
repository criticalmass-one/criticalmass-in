<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\LoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class LoginController extends AbstractController
{
    public function __construct(
        protected NotifierInterface $notifier,
        protected LoginLinkHandlerInterface $loginLinkHandler,
        protected UserRepository $userRepository
    )
    {

    }

    #[Route('/login', name: 'login')]
    public function requestLoginLink(Request $request): Response
    {
        $loginForm = $this->createForm(LoginType::class);

        $loginForm->handleRequest($request);

        // check if login form is submitted
        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $email = $loginForm->getData()['email'];

            $user = $this->userRepository->findOneBy(['email' => $email]);

            // create a login link for $user this returns an instance
            // of LoginLinkDetails
            $loginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

            $notification = new LoginLinkNotification(
                $loginLinkDetails,
                'Dein persönlicher Login-Link für criticalmass.in!' // email subject
            );

            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $this->notifier->send($notification, $recipient);

            // render a "Login link is sent!" page
            return $this->render('login/login_link_sent.html.twig', [
                'login_form' => $loginForm->createView(),
            ]);
        }

        // if it's not submitted, render the "login" form
        return $this->render('login/login.html.twig', [
            'login_form' => $loginForm->createView(),
        ]);
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
}
