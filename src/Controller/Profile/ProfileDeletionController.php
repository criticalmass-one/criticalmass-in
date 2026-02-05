<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Criticalmass\Profile\Deletion\UserDeleter;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;

class ProfileDeletionController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/delete',
        name: 'criticalmass_user_profile_delete',
        methods: ['GET'],
        priority: 180
    )]
    public function deleteAction(Security $security): Response
    {
        /** @var User $user */
        $user = $security->getUser();

        $this->denyAccessUnlessGranted('delete', $user);

        return $this->render('ProfileManagement/delete.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/delete',
        name: 'criticalmass_user_profile_delete_confirm',
        methods: ['POST'],
        priority: 180
    )]
    public function deleteConfirmAction(
        Request $request,
        Security $security,
        UserDeleter $userDeleter
    ): Response {
        /** @var User $user */
        $user = $security->getUser();

        $this->denyAccessUnlessGranted('delete', $user);

        $submittedToken = $request->request->get('_token');

        if (!$this->isCsrfTokenValid('delete-profile', $submittedToken)) {
            $this->addFlash('danger', 'Ungültiges CSRF-Token. Bitte versuche es erneut.');

            return $this->redirectToRoute('criticalmass_user_profile_delete');
        }

        $userDeleter->delete($user);

        $request->getSession()->invalidate();
        $security->logout(false);

        $this->addFlash('success', 'Dein Benutzerkonto wurde erfolgreich gelöscht.');

        return $this->redirectToRoute('frontpage');
    }
}
