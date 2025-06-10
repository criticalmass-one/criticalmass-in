<?php declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\CitySlug;
use App\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RideValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {

    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Ride::class) {
            return [];
        }

        $ride = $this->findRideById($request) ?? $this->findRideBySlugs($request);

        if (!$ride && !$argument->isNullable()) {
            throw new NotFoundHttpException('Ride not found');
        }

        return [$ride];
    }

    private function guessDateTime(string $rideDate): ?\DateTimeInterface
    {
        $parts = explode('-', $rideDate);

        return match (count($parts)) {
            2 => new \DateTime("{$parts[0]}-{$parts[1]}-01"),
            3 => new \DateTime("{$parts[0]}-{$parts[1]}-{$parts[2]}"),
            default => null,
        };
    }

    private function findRideById(Request $request): ?Ride
    {
        $rideId = $request->get('rideId');
        return $rideId ? $this->registry->getRepository(Ride::class)->find($rideId) : null;
    }

    private function findRideBySlugs(Request $request): ?Ride
    {
        $citySlug = $request->get('citySlug');
        $rideIdentifier = $request->get('rideIdentifier');

        if (!$citySlug || !$rideIdentifier) {
            return null;
        }

        preg_match('/^([0-9]{4})\-([0-9]{1,2})(?:\-?)([0-9]{1,2})?$/', $rideIdentifier, $matches);

        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBy(['slug' => $citySlug]);

        if (!$citySlug) {
            return null;
        }

        $city = $citySlug->getCity();

        $rideRepository = $this->registry->getRepository(Ride::class);

        if (count($matches) === 0) {
            return $rideRepository->findOneByCityAndSlug($city, $rideIdentifier);
        }

        $rideDateTime = $this->guessDateTime($rideIdentifier);

        if (count($matches) === 3) {
            $rides = $rideRepository->findByCityAndMonth($city, $rideDateTime);
            return array_shift($rides);
        }

        if (count($matches) === 4) {
            return $rideRepository->findCityRideByDate($city, $rideDateTime);
        }

        return null;
    }
}
