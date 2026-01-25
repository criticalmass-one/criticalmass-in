<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use App\Entity\City;
use App\Entity\Location;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use GraphQL\Type\Definition\ResolveInfo;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Resolver\ResolverMap;

class CriticalMassResolverMap extends ResolverMap
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {
    }

    protected function map(): array
    {
        return [
            'Query' => [
                // Cities
                'cities' => fn ($value, Argument $args) => $this->getCities($args),
                'city' => fn ($value, Argument $args) => $this->getCityBySlug($args['slug']),
                'cityById' => fn ($value, Argument $args) => $this->registry->getRepository(City::class)->find($args['id']),

                // Rides
                'rides' => fn ($value, Argument $args) => $this->getRides($args),
                'ride' => fn ($value, Argument $args) => $this->registry->getRepository(Ride::class)->find($args['id']),
                'rideBySlug' => fn ($value, Argument $args) => $this->getRideBySlug($args['citySlug'], $args['date']),

                // Photos
                'photos' => fn ($value, Argument $args) => $this->getPhotos($args),
                'photo' => fn ($value, Argument $args) => $this->registry->getRepository(Photo::class)->find($args['id']),

                // Tracks
                'tracks' => fn ($value, Argument $args) => $this->getTracks($args),
                'track' => fn ($value, Argument $args) => $this->registry->getRepository(Track::class)->find($args['id']),

                // Locations
                'locations' => fn ($value, Argument $args) => $this->getLocationsByCitySlug($args['citySlug']),
                'location' => fn ($value, Argument $args) => $this->registry->getRepository(Location::class)->find($args['id']),
            ],
            'City' => [
                'rides' => fn (City $city, Argument $args) => $this->getRidesForCity($city, $args),
                'locations' => fn (City $city) => $city->getLocations()->toArray(),
                'socialNetworkProfiles' => fn (City $city) => $city->getSocialNetworkProfiles()->toArray(),
            ],
            'Ride' => [
                'city' => fn (Ride $ride) => $ride->getCity(),
                'photos' => fn (Ride $ride, Argument $args) => $this->getPhotosForRide($ride, $args),
                'tracks' => fn (Ride $ride) => $ride->getTracks()->toArray(),
                'weather' => fn (Ride $ride) => $ride->getWeather(),
                'dateTime' => fn (Ride $ride) => $ride->getDateTime()?->format(\DateTimeInterface::ATOM),
            ],
            'Photo' => [
                'ride' => fn (Photo $photo) => $photo->getRide(),
                'city' => fn (Photo $photo) => $photo->getCity(),
                'exifCreationDate' => fn (Photo $photo) => $photo->getExifCreationDate()?->format(\DateTimeInterface::ATOM),
            ],
            'Track' => [
                'ride' => fn (Track $track) => $track->getRide(),
                'startDateTime' => fn (Track $track) => $track->getStartDateTime()?->format(\DateTimeInterface::ATOM),
                'endDateTime' => fn (Track $track) => $track->getEndDateTime()?->format(\DateTimeInterface::ATOM),
            ],
            'Location' => [
                'city' => fn (Location $location) => $location->getCity(),
            ],
        ];
    }

    private function getCities(Argument $args): array
    {
        $limit = $args['limit'] ?? 10;

        return $this->registry->getRepository(City::class)
            ->createQueryBuilder('c')
            ->orderBy('c.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    private function getCityBySlug(string $slug): ?City
    {
        return $this->registry->getRepository(City::class)->findOneBy(['slug' => $slug]);
    }

    private function getRides(Argument $args): array
    {
        $limit = $args['limit'] ?? 10;
        $citySlug = $args['citySlug'] ?? null;

        $qb = $this->registry->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults($limit);

        if ($citySlug) {
            $qb->join('r.city', 'c')
               ->andWhere('c.slug = :citySlug')
               ->setParameter('citySlug', $citySlug);
        }

        return $qb->getQuery()->getResult();
    }

    private function getRideBySlug(string $citySlug, string $date): ?Ride
    {
        $dateTime = new \DateTime($date);
        $startOfDay = (clone $dateTime)->setTime(0, 0, 0);
        $endOfDay = (clone $dateTime)->setTime(23, 59, 59);

        return $this->registry->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->join('r.city', 'c')
            ->where('c.slug = :citySlug')
            ->andWhere('r.dateTime BETWEEN :start AND :end')
            ->setParameter('citySlug', $citySlug)
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getRidesForCity(City $city, Argument $args): array
    {
        $limit = $args['limit'] ?? 10;

        return $this->registry->getRepository(Ride::class)
            ->createQueryBuilder('r')
            ->where('r.city = :city')
            ->orderBy('r.dateTime', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('city', $city)
            ->getQuery()
            ->getResult();
    }

    private function getPhotos(Argument $args): array
    {
        $limit = $args['limit'] ?? 10;
        $citySlug = $args['citySlug'] ?? null;
        $rideId = $args['rideId'] ?? null;

        $qb = $this->registry->getRepository(Photo::class)
            ->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit);

        if ($citySlug) {
            $qb->join('p.city', 'c')
               ->andWhere('c.slug = :citySlug')
               ->setParameter('citySlug', $citySlug);
        }

        if ($rideId) {
            $qb->andWhere('p.ride = :rideId')
               ->setParameter('rideId', $rideId);
        }

        return $qb->getQuery()->getResult();
    }

    private function getPhotosForRide(Ride $ride, Argument $args): array
    {
        $limit = $args['limit'] ?? 10;

        return $this->registry->getRepository(Photo::class)
            ->createQueryBuilder('p')
            ->where('p.ride = :ride')
            ->orderBy('p.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('ride', $ride)
            ->getQuery()
            ->getResult();
    }

    private function getTracks(Argument $args): array
    {
        $limit = $args['limit'] ?? 10;
        $rideId = $args['rideId'] ?? null;

        $qb = $this->registry->getRepository(Track::class)
            ->createQueryBuilder('t')
            ->where('t.enabled = :enabled')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('enabled', true);

        if ($rideId) {
            $qb->andWhere('t.ride = :rideId')
               ->setParameter('rideId', $rideId);
        }

        return $qb->getQuery()->getResult();
    }

    private function getLocationsByCitySlug(string $citySlug): array
    {
        return $this->registry->getRepository(Location::class)
            ->createQueryBuilder('l')
            ->join('l.city', 'c')
            ->where('c.slug = :citySlug')
            ->setParameter('citySlug', $citySlug)
            ->getQuery()
            ->getResult();
    }
}
