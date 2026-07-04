<?php declare(strict_types=1);

namespace App\Controller\Upload;

use App\Controller\AbstractController;
use App\Criticalmass\PhotoImport\PhotoCandidateImporter\PhotoCandidateImporterInterface;
use App\Criticalmass\PhotoImport\Review\CandidatePreviewThumbnailer;
use App\Criticalmass\PhotoImport\Review\UploadReviewAssembler;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use App\Repository\PhotoImportCandidateRepository;
use App\Repository\TrackImportCandidateRepository;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Unified review of everything a user has uploaded: parked/matched tracks and photo
 * galleries. Photos are confirmed, reassigned or rejected per whole gallery (never per
 * single photo); reassignment — for tracks and galleries alike — is restricted to rides
 * on the same date. Track confirm/reject reuse the existing BulkTrackReviewController.
 */
class UnifiedReviewController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/upload/review', name: 'caldera_criticalmass_unified_review', methods: ['GET'], priority: 310)]
    public function listAction(UploadReviewAssembler $assembler, #[CurrentUser] User $user): Response
    {
        /** @var TrackImportCandidateRepository $trackRepository */
        $trackRepository = $this->managerRegistry->getRepository(TrackImportCandidate::class);

        $matchedTracks = $trackRepository->findMatchedUploadCandidatesForUser($user);
        $parkedTracks = $trackRepository->findParkedUploadCandidatesForUser($user);

        $trackReassignOptions = [];
        foreach ([...$matchedTracks, ...$parkedTracks] as $candidate) {
            $trackReassignOptions[$candidate->getId()] = $assembler->ridesOnDate($candidate->getStartDateTime());
        }

        return $this->render('Upload/unified-review.html.twig', [
            'matchedTracks' => $matchedTracks,
            'parkedTracks' => $parkedTracks,
            'trackReassignOptions' => $trackReassignOptions,
            'photoGalleries' => $assembler->photoGalleries($user),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/upload/review/photos/{galleryKey}/confirm', name: 'caldera_criticalmass_unified_review_photos_confirm', requirements: ['galleryKey' => '[\w-]+'], methods: ['POST'], priority: 310)]
    public function confirmGalleryAction(
        Request $request,
        string $galleryKey,
        UploadReviewAssembler $assembler,
        PhotoCandidateImporterInterface $importer,
        LoggerInterface $logger,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'unified_review');

        $candidates = $this->galleryCandidates($user, $galleryKey);

        if ($candidates === []) {
            return $this->redirectToRoute('caldera_criticalmass_unified_review');
        }

        $ride = $this->resolveRide($request);
        $galleryDate = $candidates[0]->getExifCreationDate();

        if ($ride === null || !$this->isRideOnDate($ride, $assembler->ridesOnDate($galleryDate))) {
            $this->addFlash('danger', 'Bitte wähle eine Tour vom selben Tag aus.');

            return $this->redirectToRoute('caldera_criticalmass_unified_review');
        }

        try {
            $photos = $importer->importGallery($candidates, $ride);
            $this->addFlash('success', sprintf('%d Foto(s) wurden der Tour „%s“ zugeordnet.', count($photos), $ride->getTitle()));
        } catch (\RuntimeException $exception) {
            $logger->error('Gallery confirm failed', ['galleryKey' => $galleryKey, 'exception' => $exception]);
            $this->addFlash('danger', 'Die Galerie konnte nicht importiert werden.');
        }

        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/upload/review/photos/{galleryKey}/reject', name: 'caldera_criticalmass_unified_review_photos_reject', requirements: ['galleryKey' => '[\w-]+'], methods: ['POST'], priority: 310)]
    public function rejectGalleryAction(
        Request $request,
        string $galleryKey,
        FilesystemOperator $photoCandidateFilesystem,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'unified_review');

        $candidates = $this->galleryCandidates($user, $galleryKey);
        $manager = $this->managerRegistry->getManager();

        foreach ($candidates as $candidate) {
            $storagePath = $candidate->getStagedFilename();

            if ($storagePath !== null && $photoCandidateFilesystem->fileExists($storagePath)) {
                $photoCandidateFilesystem->delete($storagePath);
            }

            $manager->remove($candidate);
        }

        $manager->flush();

        $this->addFlash('info', sprintf('%d Foto(s) wurden verworfen.', count($candidates)));

        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/upload/review/track/{id}/reassign', name: 'caldera_criticalmass_unified_review_track_reassign', requirements: ['id' => '\d+'], methods: ['POST'], priority: 310)]
    public function reassignTrackAction(
        Request $request,
        #[MapEntity(id: 'id')] TrackImportCandidate $candidate,
        UploadReviewAssembler $assembler,
        #[CurrentUser] User $user,
    ): Response {
        $this->assertCsrf($request, 'unified_review');
        $this->assertTrackOwnership($candidate, $user);

        $ride = $this->resolveRide($request);

        if ($ride === null || !$this->isRideOnDate($ride, $assembler->ridesOnDate($candidate->getStartDateTime()))) {
            $this->addFlash('danger', 'Bitte wähle eine Tour vom selben Tag aus.');

            return $this->redirectToRoute('caldera_criticalmass_unified_review');
        }

        $candidate->setRide($ride);
        $this->managerRegistry->getManager()->flush();

        $this->addFlash('success', sprintf('Track der Tour „%s“ zugeordnet.', $ride->getTitle()));

        return $this->redirectToRoute('caldera_criticalmass_unified_review');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/upload/review/photo/{id}/preview', name: 'caldera_criticalmass_unified_review_photo_preview', requirements: ['id' => '\d+'], methods: ['GET'], priority: 310)]
    public function photoPreviewAction(
        #[MapEntity(id: 'id')] PhotoImportCandidate $candidate,
        FilesystemOperator $photoCandidateFilesystem,
        CandidatePreviewThumbnailer $thumbnailer,
        #[CurrentUser] User $user,
    ): Response {
        if ($candidate->getUser() !== $user) {
            throw new AccessDeniedHttpException();
        }

        $storagePath = $candidate->getStagedFilename();

        if ($storagePath === null || !$photoCandidateFilesystem->fileExists($storagePath)) {
            throw new NotFoundHttpException();
        }

        $bytes = $photoCandidateFilesystem->read($storagePath);
        $thumbnail = $thumbnailer->thumbnail($bytes);

        return new Response($thumbnail ?? $bytes, Response::HTTP_OK, [
            'Content-Type' => $thumbnail !== null ? 'image/jpeg' : ($candidate->getMimeType() ?? 'application/octet-stream'),
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    /**
     * @return list<PhotoImportCandidate>
     */
    private function galleryCandidates(User $user, string $galleryKey): array
    {
        /** @var PhotoImportCandidateRepository $repository */
        $repository = $this->managerRegistry->getRepository(PhotoImportCandidate::class);

        return array_values(array_filter(
            $repository->findActiveForUser($user),
            static fn (PhotoImportCandidate $candidate): bool => ($candidate->getGalleryKey() ?? UploadReviewAssembler::UNDATED_KEY) === $galleryKey,
        ));
    }

    private function resolveRide(Request $request): ?Ride
    {
        $rideId = $request->request->get('rideId');

        if (!is_numeric($rideId)) {
            return null;
        }

        return $this->managerRegistry->getRepository(Ride::class)->find((int) $rideId);
    }

    /**
     * @param list<Ride> $rides
     */
    private function isRideOnDate(Ride $ride, array $rides): bool
    {
        foreach ($rides as $candidateRide) {
            if ($candidateRide->getId() === $ride->getId()) {
                return true;
            }
        }

        return false;
    }

    private function assertCsrf(Request $request, string $tokenId): void
    {
        if (!$this->isCsrfTokenValid($tokenId, (string) $request->request->get('_token'))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    }

    private function assertTrackOwnership(TrackImportCandidate $candidate, User $user): void
    {
        if ($candidate->getUser() !== $user) {
            throw new AccessDeniedHttpException();
        }
    }
}
