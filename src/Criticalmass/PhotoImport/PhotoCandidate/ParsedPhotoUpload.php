<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\PhotoCandidate;

use App\Entity\PhotoImportCandidate;

/**
 * Result of parsing an uploaded image: the (not yet persisted) candidate with
 * its extracted EXIF metadata, plus the normalised image bytes to be staged so
 * the file can later be turned into a Photo (see PhotoCandidateImporter).
 */
final readonly class ParsedPhotoUpload
{
    public function __construct(
        private PhotoImportCandidate $candidate,
        private string $imageBytes,
    ) {
    }

    public function getCandidate(): PhotoImportCandidate
    {
        return $this->candidate;
    }

    public function getImageBytes(): string
    {
        return $this->imageBytes;
    }
}
