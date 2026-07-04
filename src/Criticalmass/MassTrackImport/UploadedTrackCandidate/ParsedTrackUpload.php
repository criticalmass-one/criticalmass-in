<?php declare(strict_types=1);

namespace App\Criticalmass\MassTrackImport\UploadedTrackCandidate;

use App\Entity\TrackImportCandidate;

/**
 * Result of parsing an uploaded GPX/FIT file: the (not yet persisted) candidate
 * with its extracted metadata, plus the normalised GPX XML to be stored so the
 * file can later be turned into a Track (see FileTrackImporter, #1386).
 */
final class ParsedTrackUpload
{
    public function __construct(
        private readonly TrackImportCandidate $candidate,
        private readonly string $gpxXml,
    ) {
    }

    public function getCandidate(): TrackImportCandidate
    {
        return $this->candidate;
    }

    public function getGpxXml(): string
    {
        return $this->gpxXml;
    }
}
