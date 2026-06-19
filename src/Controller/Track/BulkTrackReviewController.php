<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\MassTrackImport\FileTrackImporter\FileTrackImporterInterface;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Repository\TrackImportCandidateRepository;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Review and confirmation of bulk-uploaded track candidates (the upload replacement for
 * the former Strava mass import). Matched candidates can be confirmed individually or all
 * at once; parked (unmatched) candidates can only be discarded. Strava review lives in
 * StravaMassImportController and is left untouched.
 */
class BulkTrackReviewController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/review', name: 'caldera_criticalmass_track_bulkupload_review', methods: ['GET'], priority: 310)]
    public function listAction(): Response
    {
        // Track review has been folded into the unified review; keep the route for
        // existing links/bookmarks and redirect to the combined page.
        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/review/{id}/confirm', name: 'caldera_criticalmass_track_bulkupload_confirm', requirements: ['id' => '\d+'], methods: ['POST'], priority: 310)]
    public function confirmAction(
        Request $request,
        #[MapEntity(id: 'id')] TrackImportCandidate $candidate,
        FileTrackImporterInterface $fileTrackImporter,
        LoggerInterface $logger,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'bulk_track_review');
        $this->assertOwnership($candidate, $user);

        if ($candidate->getRide() === null) {
            return $this->redirectToRoute('caldera_criticalmass_unified_review');
        }

        $rideTitle = $candidate->getRide()->getTitle();

        try {
            $fileTrackImporter->importCandidate($candidate);
            $this->addFlash('success', sprintf('Track für „%s" wurde importiert.', $rideTitle));
        } catch (\RuntimeException $exception) {
            $logger->error('Bulk track confirm failed', ['candidate' => $candidate->getId(), 'exception' => $exception]);
            $this->addFlash('danger', sprintf('Track für „%s" konnte nicht importiert werden.', $rideTitle));
        }

        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/review/confirm-all', name: 'caldera_criticalmass_track_bulkupload_confirm_all', methods: ['POST'], priority: 310)]
    public function confirmAllAction(
        Request $request,
        FileTrackImporterInterface $fileTrackImporter,
        LoggerInterface $logger,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'bulk_track_review');

        /** @var TrackImportCandidateRepository $repository */
        $repository = $this->managerRegistry->getRepository(TrackImportCandidate::class);
        $candidates = $repository->findMatchedUploadCandidatesForUser($user);

        $imported = 0;
        $failed = 0;

        foreach ($candidates as $candidate) {
            try {
                $fileTrackImporter->importCandidate($candidate);
                ++$imported;
            } catch (\RuntimeException $exception) {
                // A single broken candidate must not abort the whole batch.
                $logger->error('Bulk track confirm-all failed for candidate', ['candidate' => $candidate->getId(), 'exception' => $exception]);
                ++$failed;
            }
        }

        $this->addFlash('success', sprintf('%d zugeordnete Track(s) wurden importiert.', $imported));

        if ($failed > 0) {
            $this->addFlash('warning', sprintf('%d Datei(en) konnten nicht importiert werden und bleiben zur Prüfung erhalten.', $failed));
        }

        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/review/{id}/reject', name: 'caldera_criticalmass_track_bulkupload_reject', requirements: ['id' => '\d+'], methods: ['POST'], priority: 310)]
    public function rejectAction(
        Request $request,
        #[MapEntity(id: 'id')] TrackImportCandidate $candidate,
        FilesystemOperator $trackFilesystem,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'bulk_track_review');
        $this->assertOwnership($candidate, $user);

        $storagePath = $candidate->getTrackFilename();

        if ($storagePath !== null && $trackFilesystem->fileExists($storagePath)) {
            $trackFilesystem->delete($storagePath);
        }

        $manager = $this->managerRegistry->getManager();
        $manager->remove($candidate);
        $manager->flush();

        $this->addFlash('info', 'Kandidat wurde verworfen.');

        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    private function assertCsrf(Request $request, string $tokenId): void
    {
        if (!$this->isCsrfTokenValid($tokenId, (string) $request->request->get('_token'))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    }

    private function assertOwnership(TrackImportCandidate $candidate, User $user): void
    {
        if ($candidate->getUser() !== $user) {
            throw new AccessDeniedHttpException();
        }
    }
}
