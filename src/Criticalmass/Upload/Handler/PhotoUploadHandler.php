<?php declare(strict_types=1);

namespace App\Criticalmass\Upload\Handler;

use App\Criticalmass\PhotoImport\PhotoCandidate\PhotoCandidateFactory;
use App\Criticalmass\Upload\UploadResult;
use App\Entity\User;
use App\Repository\PhotoImportCandidateRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemOperator;

/**
 * Stages a single uploaded image: parse it into a photo candidate (HEIC normalised
 * to JPEG, EXIF date/GPS extracted), store the staged bytes and park the candidate.
 *
 * Unlike tracks, photos are not matched to a ride here — matching happens per gallery
 * on the review page, where all photos sharing a capture date are grouped together.
 */
class PhotoUploadHandler
{
    public function __construct(
        private readonly PhotoCandidateFactory $candidateFactory,
        private readonly FilesystemOperator $photoCandidateFilesystem,
        private readonly PhotoImportCandidateRepository $candidateRepository,
        private readonly ManagerRegistry $registry,
    ) {
    }

    /**
     * @throws \RuntimeException if the image format is unsupported or cannot be read/converted
     */
    public function handle(string $filePath, string $originalName, User $user): UploadResult
    {
        $parsed = $this->candidateFactory->createFromUpload($filePath, $originalName, $user);

        // The factory normalises by extension; make sure what we stage is actually a
        // decodable image (getimagesizefromstring is core, so this also holds in CI).
        if (false === @getimagesizefromstring($parsed->getImageBytes())) {
            throw new \RuntimeException('Die Datei ist kein gültiges Bild und konnte nicht verarbeitet werden.');
        }

        $candidate = $parsed->getCandidate();
        $fileHash = (string) $candidate->getFileHash();

        if (null !== $this->candidateRepository->findOneByUserAndFileHash($user, $fileHash)) {
            return new UploadResult(UploadResult::KIND_PHOTO, UploadResult::STATUS_DUPLICATE, 'Dieses Bild hast du bereits hochgeladen.');
        }

        $this->photoCandidateFilesystem->write((string) $candidate->getStagedFilename(), $parsed->getImageBytes());

        $manager = $this->registry->getManager();
        $manager->persist($candidate);
        $manager->flush();

        return new UploadResult(UploadResult::KIND_PHOTO, UploadResult::STATUS_STAGED, 'Bild gespeichert — du kannst es gleich einer Tour zuordnen.');
    }
}
