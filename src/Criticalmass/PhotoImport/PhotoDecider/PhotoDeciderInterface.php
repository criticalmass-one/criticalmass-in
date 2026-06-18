<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\PhotoDecider;

use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;

interface PhotoDeciderInterface
{
    /**
     * Suggests a ride for a gallery of photo candidates that share a capture date,
     * or null when no confident match exists (the gallery is then parked for manual
     * assignment in the review UI).
     *
     * @param list<PhotoImportCandidate> $candidates
     */
    public function decideForGallery(?\DateTime $galleryDate, array $candidates): ?Ride;
}
