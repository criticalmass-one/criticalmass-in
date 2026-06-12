<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\FileTrackImporter;

use App\Entity\Track;
use App\Entity\TrackImportCandidate;

interface FileTrackImporterInterface
{
    /**
     * Turns a confirmed, ride-assigned upload candidate into a real Track and
     * removes the candidate (and its stored file).
     *
     * @throws \RuntimeException if the candidate has no ride or its file is missing
     */
    public function importCandidate(TrackImportCandidate $candidate): Track;
}
