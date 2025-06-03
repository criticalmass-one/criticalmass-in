<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\LoginType;
use App\Notifier\CriticalMassLoginLinkNotification;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class LoginController extends AbstractController
{
    const string DEFAULT_USERNAME = 'anonymous cyclist';

    public function __construct(
        protected UserRepository $userRepository,
        protected ManagerRegistry $managerRegistry
    )
    {

    }

    #[Route('/login', name: 'login', methods: ['GET'])]
    public function login(): Response
    {
       $loginForm = $this->createForm(LoginType::class);

        return $this->render('security/login.html.twig', [
            'login_form' => $loginForm->createView(),
        ]);
    }

    #[Route('/login', name: 'login_perform', methods: ['POST'])]
    public function loginPerform(
        NotifierInterface $notifier,
        LoginLinkHandlerInterface $loginLinkHandler,
        UserRepository $userRepository,
        Request $request
    ): Response {
        $form = $this->createForm(LoginType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $email = $data['email'];

            $user = $userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $user = $this->createNewUser($email);
            }

            $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

            // create a notification based on the login link details
            $notification = new CriticalMassLoginLinkNotification(
                $loginLinkDetails,
                'Dein Login auf criticalmass.in'
            );
            // create a recipient for this user
            $recipient = new Recipient($user->getEmail());

            // send the notification to the user
            $notifier->send($notification, $recipient);

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig');
        }

        return $this->redirectToRoute('login');
    }

    public function createNewUser(string $email): User
    {
        $user = new User();
        $user
            ->setEmail($email)
            ->setUsername(self::DEFAULT_USERNAME)
        ;

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
}
