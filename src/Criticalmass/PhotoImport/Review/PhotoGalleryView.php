<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\Review;

use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;

/**
 * View model for one photo gallery on the unified review page: all staged photos that
 * share a capture date, the ride the decider suggests for them, and the rides on that
 * day the user may reassign the whole gallery to.
 */
final readonly class PhotoGalleryView
{
    /**
     * @param list<PhotoImportCandidate> $candidates
     * @param list<Ride>                 $sameDateRides
     */
    public function __construct(
        public string $key,
        public ?\DateTime $date,
        public array $candidates,
        public ?Ride $suggestedRide,
        public array $sameDateRides,
    ) {
    }

    public function isDated(): bool
    {
        return $this->date !== null;
    }

    public function count(): int
    {
        return count($this->candidates);
    }

    public function canBeAssigned(): bool
    {
        return $this->sameDateRides !== [];
    }
}
