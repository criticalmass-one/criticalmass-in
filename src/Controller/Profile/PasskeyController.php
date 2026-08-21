<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Entity\User;
use App\Entity\WebauthnCredential;
use App\Repository\WebauthnCredentialRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Verwaltung der eigenen Passkeys.
 *
 * Angelegt werden sie nicht hier, sondern über die Endpunkte des Bundles unter
 * /passkey/register — die Ceremony muss der Browser fahren. Diese Seite listet auf, was
 * dabei herauskam, und lässt es umbenennen und löschen.
 */
class PasskeyController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route(
        '/profile/passkeys',
        name: 'criticalmass_user_profile_passkey_list',
        methods: ['GET'],
        priority: 180
    )]
    public function listAction(
        WebauthnCredentialRepository $credentialRepository,
        ?UserInterface $user = null
    ): Response {
        return $this->render('ProfileManagement/passkeys.html.twig', [
            'credentials' => $credentialRepository->findAllForUser($this->requireUser($user)),
        ]);
    }

    /**
     * Das Credential kommt über seine ID aus der URL — ohne den Voter könnte jeder
     * angemeldete Nutzer fremde Passkeys umbenennen.
     */
    #[IsGranted('edit', 'credential')]
    #[Route(
        '/profile/passkeys/{id}/rename',
        name: 'criticalmass_user_profile_passkey_rename',
        methods: ['POST'],
        priority: 180
    )]
    public function renameAction(
        Request $request,
        ManagerRegistry $managerRegistry,
        WebauthnCredential $credential
    ): Response {
        if (!$this->isCsrfTokenValid('passkey-rename-' . $credential->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Ungültiges CSRF-Token.');
        }

        $name = trim((string) $request->request->get('name'));

        if ('' === $name) {
            $this->addFlash('danger', 'Der Passkey braucht einen Namen.');

            return $this->redirectToRoute('criticalmass_user_profile_passkey_list');
        }

        $credential->setName(mb_substr($name, 0, 255));

        $managerRegistry->getManager()->flush();

        $this->addFlash('success', sprintf('Der Passkey heißt jetzt %s.', $credential->getName()));

        return $this->redirectToRoute('criticalmass_user_profile_passkey_list');
    }

    #[IsGranted('delete', 'credential')]
    #[Route(
        '/profile/passkeys/{id}/delete',
        name: 'criticalmass_user_profile_passkey_delete',
        methods: ['POST'],
        priority: 180
    )]
    public function deleteAction(
        Request $request,
        WebauthnCredentialRepository $credentialRepository,
        WebauthnCredential $credential
    ): Response {
        if (!$this->isCsrfTokenValid('passkey-delete-' . $credential->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Ungültiges CSRF-Token.');
        }

        $name = $credential->getName();

        $credentialRepository->remove($credential);

        $this->addFlash('success', sprintf('Der Passkey %s wurde gelöscht.', $name));

        return $this->redirectToRoute('criticalmass_user_profile_passkey_list');
    }

    /**
     * IsGranted('ROLE_USER') stellt bereits sicher, dass jemand angemeldet ist. Diese
     * Einengung auf unsere User-Entity ist für die statische Analyse.
     */
    private function requireUser(?UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Kein Benutzerkonto in der Sitzung.');
        }

        return $user;
    }
}
