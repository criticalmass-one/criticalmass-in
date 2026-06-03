<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Criticalmass\MassTrackImport\ProposalPersister\ProposalPersisterInterface;
use App\Criticalmass\MassTrackImport\TrackDecider\TrackDeciderInterface;
use App\Criticalmass\MassTrackImport\UploadedTrackCandidate\UploadedTrackCandidateFactory;
use App\Entity\TrackImportCandidate;
use App\Entity\User;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Per-file endpoint for the bulk track upload (Dropzone sends one request per file).
 * Each file is parsed, stored as normalised GPX, and either matched to a ride or
 * parked for manual review — the upload replacement for the former Strava mass import.
 */
class BulkTrackUploadController extends AbstractController
{
    private const CANDIDATE_DIRECTORY = 'candidates';

    #[IsGranted('ROLE_USER')]
    #[Route('/trackupload/bulk/file', name: 'caldera_criticalmass_track_bulkupload_file', methods: ['POST'], priority: 310)]
    public function uploadFileAction(
        Request $request,
        UploadedTrackCandidateFactory $candidateFactory,
        TrackDeciderInterface $trackDecider,
        ProposalPersisterInterface $proposalPersister,
        FilesystemOperator $trackFilesystem,
        #[CurrentUser] ?User $user = null,
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('bulk_track_upload', (string) $request->request->get('_token'))) {
            return $this->statusResponse('error', 'Invalid CSRF token.', Response::HTTP_FORBIDDEN);
        }

        if (!$user instanceof User) {
            return $this->statusResponse('error', 'Authentication required.', Response::HTTP_FORBIDDEN);
        }

        $uploadedFile = $request->files->get('file');

        if (!$uploadedFile instanceof UploadedFile) {
            return $this->statusResponse('error', 'No file was uploaded.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $parsed = $candidateFactory->createFromUpload(
                $uploadedFile->getPathname(),
                $uploadedFile->getClientOriginalName(),
                $user,
            );
        } catch (\RuntimeException $exception) {
            return $this->statusResponse('error', $exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $candidate = $parsed->getCandidate();
        $fileHash = (string) $candidate->getFileHash();

        if ($this->candidateAlreadyExists($user, $fileHash)) {
            return $this->statusResponse('duplicate', 'This file has already been uploaded.');
        }

        $storagePath = sprintf('%s/%s.gpx', self::CANDIDATE_DIRECTORY, $fileHash);
        $trackFilesystem->write($storagePath, $parsed->getGpxXml());
        $candidate->setTrackFilename($storagePath);

        $rideResult = $trackDecider->decide($candidate);

        if ($rideResult !== null) {
            $proposalPersister->persist($rideResult);

            return $this->statusResponse('matched', sprintf('Assigned to ride "%s".', $rideResult->getRide()->getTitle()));
        }

        // No confident ride match → park the candidate without a ride for manual review.
        // (The decider may have pre-set a below-threshold ride, so reset it explicitly.)
        $candidate->setRide(null);

        $manager = $this->managerRegistry->getManager();
        $manager->persist($candidate);
        $manager->flush();

        return $this->statusResponse('parked', 'No matching ride found — kept for review.');
    }

    private function candidateAlreadyExists(User $user, string $fileHash): bool
    {
        return null !== $this->managerRegistry->getRepository(TrackImportCandidate::class)->findOneBy([
            'user' => $user,
            'fileHash' => $fileHash,
        ]);
    }

    /**
     * @param 'matched'|'parked'|'duplicate'|'error' $status
     */
    private function statusResponse(string $status, string $message, int $httpStatus = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['status' => $status, 'message' => $message], $httpStatus);
    }
}
