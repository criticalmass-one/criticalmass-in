<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\PhotoCandidateImporter;

use App\Entity\Photo;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;

interface PhotoCandidateImporterInterface
{
    /**
     * Turns a confirmed gallery of photo candidates into Photos assigned to the
     * given ride, consuming the candidates and their staged files.
     *
     * @param list<PhotoImportCandidate> $candidates
     *
     * @return list<Photo>
     */
    public function importGallery(array $candidates, Ride $ride): array;
}
