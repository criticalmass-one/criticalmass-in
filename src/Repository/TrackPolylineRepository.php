<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Track;
use App\Entity\TrackPolyline;
use App\Enum\PolylineResolution;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<TrackPolyline> */
class TrackPolylineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TrackPolyline::class);
    }

    public function findByTrackAndResolution(Track $track, PolylineResolution $resolution): ?TrackPolyline
    {
        return $this->findOneBy([
            'track' => $track,
            'resolution' => $resolution->value,
        ]);
    }

    /** @return TrackPolyline[] */
    public function findByTrack(Track $track): array
    {
        return $this->findBy(['track' => $track]);
    }

    public function removeByTrack(Track $track): void
    {
        $this->createQueryBuilder('tp')
            ->delete()
            ->where('tp.track = :track')
            ->setParameter('track', $track)
            ->getQuery()
            ->execute();
    }
}
