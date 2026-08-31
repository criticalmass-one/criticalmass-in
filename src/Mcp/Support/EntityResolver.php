<?php declare(strict_types=1);

namespace App\Mcp\Support;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Location;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Mcp\Tool\McpToolException;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Löst Städte und Rides aus Slugs/Identifiern auf — gemeinsam genutzt von den
 * MCP-Tools, analog zu den ValueResolvern der API. Wirft {@see McpToolException}
 * mit einer verständlichen Meldung, wenn nichts gefunden wird.
 */
final class EntityResolver
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly RideRepository $rideRepository,
    ) {
    }

    public function city(string $citySlug): City
    {
        $citySlug = trim($citySlug);

        if ('' === $citySlug) {
            throw new McpToolException('citySlug ist erforderlich.');
        }

        $slug = $this->registry->getRepository(CitySlug::class)->findOneBy(['slug' => $citySlug]);
        $city = $slug?->getCity();

        if (null === $city) {
            throw new McpToolException(sprintf('Unbekannte Stadt: "%s".', $citySlug));
        }

        return $city;
    }

    /**
     * Löst einen Ride über citySlug + Identifier (Datum YYYY-MM-DD oder Slug) auf.
     */
    public function ride(string $citySlug, string $rideIdentifier): Ride
    {
        $citySlug = trim($citySlug);
        $rideIdentifier = trim($rideIdentifier);

        if ('' === $citySlug || '' === $rideIdentifier) {
            throw new McpToolException('citySlug und rideIdentifier sind erforderlich.');
        }

        try {
            $ride = $this->rideRepository->findByCitySlugAndRideDate($citySlug, $rideIdentifier);
        } catch (\Exception) {
            // rideIdentifier ist kein gültiges Datum → als Slug behandeln.
            $ride = null;
        }

        $ride ??= $this->rideRepository->findOneByCitySlugAndSlug($citySlug, $rideIdentifier);

        if (null === $ride) {
            throw new McpToolException(sprintf('Kein Ride gefunden für "%s/%s".', $citySlug, $rideIdentifier));
        }

        return $ride;
    }

    public function rideEstimate(int $id): RideEstimate
    {
        $estimate = $this->registry->getRepository(RideEstimate::class)->find($id);

        if (null === $estimate) {
            throw new McpToolException(sprintf('Keine Schätzung mit der ID %d gefunden.', $id));
        }

        return $estimate;
    }

    /**
     * Löst eine Location über citySlug + Location-Slug auf.
     */
    public function location(string $citySlug, string $locationSlug): Location
    {
        $city = $this->city($citySlug);
        $locationSlug = trim($locationSlug);

        if ('' === $locationSlug) {
            throw new McpToolException('locationSlug ist erforderlich.');
        }

        $location = $this->registry->getRepository(Location::class)->findOneBy([
            'city' => $city,
            'slug' => $locationSlug,
        ]);

        if (null === $location) {
            throw new McpToolException(sprintf('Keine Location "%s" in Stadt "%s" gefunden.', $locationSlug, $citySlug));
        }

        return $location;
    }
}
