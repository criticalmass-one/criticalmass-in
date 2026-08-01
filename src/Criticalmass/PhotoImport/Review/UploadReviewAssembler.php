<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\Review;

use App\Criticalmass\PhotoImport\PhotoDecider\PhotoDeciderInterface;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Entity\User;
use App\Repository\PhotoImportCandidateRepository;
use App\Repository\RideRepository;

/**
 * Builds the photo side of the unified review page: groups a user's staged photos into
 * galleries by capture date and, for each, works out the suggested ride and the rides on
 * that day the gallery may be (re)assigned to.
 */
class UploadReviewAssembler
{
    public const UNDATED_KEY = 'undated';

    public function __construct(
        private readonly PhotoImportCandidateRepository $photoCandidateRepository,
        private readonly RideRepository $rideRepository,
        private readonly PhotoDeciderInterface $photoDecider,
    ) {
    }

    /**
     * @return list<PhotoGalleryView>
     */
    public function photoGalleries(User $user): array
    {
        $candidates = $this->photoCandidateRepository->findActiveForUser($user);

        /** @var array<string, list<PhotoImportCandidate>> $groups */
        $groups = [];

        foreach ($candidates as $candidate) {
            $key = $candidate->getGalleryKey() ?? self::UNDATED_KEY;
            $groups[$key][] = $candidate;
        }

        $galleries = [];

        foreach ($groups as $key => $groupCandidates) {
            $date = $groupCandidates === [] ? null : $groupCandidates[0]->getExifCreationDate();
            $sameDateRides = $this->ridesOnDate($date);
            $suggestedRide = $this->photoDecider->decideForGallery($date, $groupCandidates);

            $galleries[] = new PhotoGalleryView($key, $date, $groupCandidates, $suggestedRide, $sameDateRides);
        }

        return $galleries;
    }

    /**
     * Rides taking place on the given day — the candidates a track or gallery may be
     * (re)assigned to (reassignment is restricted to the same date).
     *
     * @return list<Ride>
     */
    public function ridesOnDate(?\DateTime $date): array
    {
        if ($date === null) {
            return [];
        }

        /** @var list<Ride> $rides */
        $rides = $this->rideRepository->findByDate($date);

        return $rides;
    }
}
