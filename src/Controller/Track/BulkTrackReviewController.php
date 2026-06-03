<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\MassTrackImport\FileTrackImporter\FileTrackImporterInterface;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Repository\TrackImportCandidateRepository;
use League\Flysystem\FilesystemOperator;
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
    public function listAction(#[CurrentUser] User $user): Response
    {
        /** @var TrackImportCandidateRepository $repository */
        $repository = $this->managerRegistry->getRepository(TrackImportCandidate::class);

        return $this->render('Track/bulk-review.html.twig', [
            'matchedCandidates' => $repository->findMatchedUploadCandidatesForUser($user),
            'parkedCandidates' => $repository->findParkedUploadCandidatesForUser($user),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/review/{id}/confirm', name: 'caldera_criticalmass_track_bulkupload_confirm', requirements: ['id' => '\d+'], methods: ['POST'], priority: 310)]
    public function confirmAction(
        Request $request,
        #[MapEntity(id: 'id')] TrackImportCandidate $candidate,
        FileTrackImporterInterface $fileTrackImporter,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'bulk_track_review');
        $this->assertOwnership($candidate, $user);

        if ($candidate->getRide() !== null) {
            $fileTrackImporter->importCandidate($candidate);
            $this->addFlash('success', sprintf('Track für „%s" wurde importiert.', $candidate->getRide()->getTitle()));
        }

        return $this->redirectToRoute('caldera_criticalmass_track_bulkupload_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/review/confirm-all', name: 'caldera_criticalmass_track_bulkupload_confirm_all', methods: ['POST'], priority: 310)]
    public function confirmAllAction(
        Request $request,
        FileTrackImporterInterface $fileTrackImporter,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'bulk_track_review');

        /** @var TrackImportCandidateRepository $repository */
        $repository = $this->managerRegistry->getRepository(TrackImportCandidate::class);
        $candidates = $repository->findMatchedUploadCandidatesForUser($user);

        $imported = 0;

        foreach ($candidates as $candidate) {
            $fileTrackImporter->importCandidate($candidate);
            ++$imported;
        }

        $this->addFlash('success', sprintf('%d zugeordnete Track(s) wurden importiert.', $imported));

        return $this->redirectToRoute('caldera_criticalmass_track_bulkupload_review');
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

        return $this->redirectToRoute('caldera_criticalmass_track_bulkupload_review');
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
