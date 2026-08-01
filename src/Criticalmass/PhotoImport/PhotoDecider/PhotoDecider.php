<?php declare(strict_types=1);

namespace App\Criticalmass\PhotoImport\PhotoDecider;

use App\Criticalmass\Geo\Coord\Coord;
use App\Criticalmass\Geo\GeoUtil\GeoUtil;
use App\Entity\PhotoImportCandidate;
use App\Entity\Ride;
use App\Repository\RideRepository;

/**
 * Decides which ride a gallery of photos belongs to.
 *
 * Photos are grouped into a gallery by their capture date, and that date is the
 * primary signal: a gallery can only match a ride that took place the same day.
 * Among the rides on that day the decision is then refined by GPS proximity — the
 * average position of the geotagged photos against each ride's location. When the
 * photos carry no GPS at all, a match is only made if exactly one ride happened
 * that day; otherwise the gallery is parked for manual assignment.
 */
class PhotoDecider implements PhotoDeciderInterface
{
    private const MATCH_RADIUS_KM = 50.0;

    public function __construct(
        private readonly RideRepository $rideRepository,
    ) {
    }

    public function decideForGallery(?\DateTime $galleryDate, array $candidates): ?Ride
    {
        if ($galleryDate === null) {
            return null;
        }

        /** @var list<Ride> $rides */
        $rides = $this->rideRepository->findByDate($galleryDate);

        if (count($rides) === 0) {
            return null;
        }

        $galleryCoord = $this->averageCoord($candidates);

        if ($galleryCoord === null) {
            // No GPS to disambiguate — only safe when the day had a single ride.
            return count($rides) === 1 ? $rides[0] : null;
        }

        return $this->nearestRideWithinRadius($rides, $galleryCoord);
    }

    /**
     * @param list<PhotoImportCandidate> $candidates
     */
    private function averageCoord(array $candidates): ?Coord
    {
        $latitudes = [];
        $longitudes = [];

        foreach ($candidates as $candidate) {
            $latitude = $candidate->getLatitude();
            $longitude = $candidate->getLongitude();

            if ($latitude !== null && $longitude !== null) {
                $latitudes[] = $latitude;
                $longitudes[] = $longitude;
            }
        }

        if ($latitudes === []) {
            return null;
        }

        return new Coord(
            array_sum($latitudes) / count($latitudes),
            array_sum($longitudes) / count($longitudes),
        );
    }

    /**
     * @param list<Ride> $rides
     */
    private function nearestRideWithinRadius(array $rides, Coord $galleryCoord): ?Ride
    {
        $nearestRide = null;
        $nearestDistance = self::MATCH_RADIUS_KM;

        foreach ($rides as $ride) {
            $latitude = $ride->getLatitude();
            $longitude = $ride->getLongitude();

            if ($latitude === null || $longitude === null) {
                continue;
            }

            $distance = GeoUtil::calculateDistance(
                new Coord($latitude, $longitude),
                $galleryCoord,
            );

            if ($distance <= $nearestDistance) {
                $nearestDistance = $distance;
                $nearestRide = $ride;
            }
        }

        return $nearestRide;
    }
}
