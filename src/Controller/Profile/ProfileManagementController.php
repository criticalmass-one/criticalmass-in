<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\Participation;
use App\Entity\Photo;
use App\Entity\Track;
use App\Form\Type\UserEmailType;
use App\Form\Type\UsernameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileManagementController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function manageAction(UserInterface $user = null): Response
    {
        $participationCounter = $this->getDoctrine()->getRepository(Participation::class)->countByUser($user);
        $trackCounter = $this->getDoctrine()->getRepository(Track::class)->countByUser($user);
        $photoCounter = $this->getDoctrine()->getRepository(Photo::class)->countByUser($user);

        return $this->render('ProfileManagement/manage.html.twig', [
            'participationCounter' => $participationCounter,
            'trackCounter' => $trackCounter,
            'photoCounter' => $photoCounter,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editUsernameAction(Request $request, UserInterface $user = null): Response
    {
        $usernameForm = $this->createForm(UsernameType::class, $user, [
            'action' => $this->generateUrl('criticalmass_user_usermanagement_editusername')
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $usernameForm->handleRequest($request);

            if ($usernameForm->isSubmitted() && $usernameForm->isValid()) {
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                try {
                    $userManager->updateUser($user);

                    $this->addFlash('success',
                        'Deine neuer Benutzername wurde gespeichert. Du heißt jetzt ' . $user->getUsername() . '!');

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

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function editEmailAction(Request $request, UserInterface $user = null): Response
    {
        $userEmailForm = $this->createForm(UserEmailType::class, $user, [
            'action' => $this->generateUrl('criticalmass_user_usermanagement_editemail')
        ]);

        if ($request->isMethod(Request::METHOD_POST)) {
            $userEmailForm->handleRequest($request);

            if ($userEmailForm->isSubmitted() && $userEmailForm->isValid()) {
                /** @var $userManager UserManagerInterface */
                $userManager = $this->get('fos_user.user_manager');

                try {
                    $userManager->updateUser($user);

                    $this->addFlash('success',
                        'Deine neue E-Mail-Adresse wurde gespeichert. Du kannst dich ab jetzt mit ' . $user->getEmail() . ' einloggen.');

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
