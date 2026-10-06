<?php declare(strict_types=1);

namespace App\Controller\Moderation;

use App\Controller\AbstractController;
use App\Criticalmass\Moderation\PostModeration;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostReportRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ModerationController extends AbstractController
{
    #[Route('/moderation/reports', name: 'caldera_criticalmass_moderation_reports', priority: 120)]
    public function reportsAction(PostReportRepository $reportRepository): Response
    {
        return $this->render('Moderation/reports.html.twig', [
            'groups' => $reportRepository->findOpenGroupedByPost(),
        ]);
    }

    #[Route('/moderation/post/{postId}/remove', requirements: ['postId' => '\d+'], name: 'caldera_criticalmass_moderation_remove', methods: ['POST'], priority: 120)]
    public function removeAction(
        Request $request,
        PostModeration $moderation,
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post
    ): Response {
        $this->denyInvalidToken($request);

        // Die Begruendung geht an den Autor (Art. 17 DSA); ohne sie wird nichts entfernt.
        $reason = trim((string) $request->request->get('reason', ''));

        if ('' === $reason) {
            $this->addFlash('danger', 'Bitte begründe, warum der Beitrag entfernt wird. Die Begründung geht an den Autor.');

            return $this->redirectToRoute('caldera_criticalmass_moderation_reports');
        }

        /** @var User $moderator */
        $moderator = $this->getUser();

        $count = $moderation->remove($post, $moderator, $reason);

        $this->addFlash('success', sprintf('Der Beitrag wurde entfernt, %d %s geschlossen.', $count, 1 === $count ? 'Meldung' : 'Meldungen'));

        return $this->redirectToRoute('caldera_criticalmass_moderation_reports');
    }

    #[Route('/moderation/post/{postId}/dismiss', requirements: ['postId' => '\d+'], name: 'caldera_criticalmass_moderation_dismiss', methods: ['POST'], priority: 120)]
    public function dismissAction(
        Request $request,
        PostModeration $moderation,
        #[MapEntity(mapping: ['postId' => 'id'])] Post $post
    ): Response {
        $this->denyInvalidToken($request);

        /** @var User $moderator */
        $moderator = $this->getUser();

        $note = trim((string) $request->request->get('reason', ''));
        $count = $moderation->dismiss($post, $moderator, '' === $note ? null : $note);

        $this->addFlash('success', sprintf('Der Beitrag bleibt stehen, %d %s geschlossen.', $count, 1 === $count ? 'Meldung' : 'Meldungen'));

        return $this->redirectToRoute('caldera_criticalmass_moderation_reports');
    }

    private function denyInvalidToken(Request $request): void
    {
        if (!$this->isCsrfTokenValid('post-moderation', (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Ungültiges Formular-Token.');
        }
    }
}
