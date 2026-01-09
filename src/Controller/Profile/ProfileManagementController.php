<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\Participation;
use App\Entity\Photo;
use App\Entity\Track;
use App\Form\Type\UserEmailType;
use App\Form\Type\UsernameType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ProfileManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/',
        name: 'criticalmass_user_usermanagement',
        priority: 180
    )]
    #[Route(
        '/profile/',
        name: 'fos_user_profile_show',
        priority: 180
    )]
    public function manageAction(UserInterface $user = null): Response
    {
        $participationCounter = $this->managerRegistry->getRepository(Participation::class)->countByUser($user);
        $trackCounter = $this->managerRegistry->getRepository(Track::class)->countByUser($user);
        $photoCounter = $this->managerRegistry->getRepository(Photo::class)->countByUser($user);

        return $this->render('ProfileManagement/manage.html.twig', [
            'participationCounter' => $participationCounter,
            'trackCounter' => $trackCounter,
            'photoCounter' => $photoCounter,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/username',
        name: 'criticalmass_user_usermanagement_editusername',
        priority: 180
    )]
    public function editUsernameAction(
        Request $request,
        ManagerRegistry $managerRegistry,
        UserInterface $user = null
    ): Response {
        $usernameForm = $this->createForm(UsernameType::class, $user, [
            'action' => $this->generateUrl('criticalmass_user_usermanagement_editusername')
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $usernameForm->handleRequest($request);

            if ($usernameForm->isSubmitted() && $usernameForm->isValid()) {
                try {
                    $managerRegistry->getManager()->flush();

                    $this->addFlash(
                        'success',
                        sprintf(
                            'Deine neuer Benutzername wurde gespeichert. Du heißt jetzt %s!',
                            $user->getUsername()
                        )
                    );

                    return $this->redirectToRoute('criticalmass_user_usermanagement');
                } catch (UniqueConstraintViolationException $exception) {
                    $error = new FormError('Dieser Benutzername ist bereits vergeben.');

                    $usernameForm->get('username')->addError($error);
                }
            }
        }

        return $this->render('ProfileManagement/edit_username.html.twig', [
            'usernameForm' => $usernameForm->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/email',
        name: 'criticalmass_user_usermanagement_editemail',
        priority: 180
    )]
    public function editEmailAction(
        Request $request,
        ManagerRegistry $managerRegistry,
        UserInterface $user = null
    ): Response {
        $userEmailForm = $this->createForm(UserEmailType::class, $user, [
            'action' => $this->generateUrl('criticalmass_user_usermanagement_editemail')
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $userEmailForm->handleRequest($request);

            if ($userEmailForm->isSubmitted() && $userEmailForm->isValid()) {
                try {
                    $managerRegistry->getManager()->flush();

                    $this->addFlash(
                        'success',
                        sprintf(
                            'Deine neue E-Mail-Adresse wurde gespeichert. Du kannst dich ab jetzt mit %s einloggen.',
                            $user->getEmail()
                        )
                    );

                    return $this->redirectToRoute('criticalmass_user_usermanagement');
                } catch (UniqueConstraintViolationException $exception) {
                    $error = new FormError('Diese E-Mail-Adresse ist bereits registriert worden.');

                    $userEmailForm->get('email')->addError($error);
                }
            }
        }

        return $this->render('ProfileManagement/edit_email.html.twig', [
            'userEmailForm' => $userEmailForm->createView(),
        ]);
    }
}
