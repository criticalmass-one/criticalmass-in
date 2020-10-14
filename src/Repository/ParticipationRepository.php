<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\Participation;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;

class ParticipationRepository extends EntityRepository
{
    public function findParticipationForUserAndRide(User $user, Ride $ride): ?Participation
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('p')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('p.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->setMaxResults(1);

        $query = $builder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function countParticipationsForRide(Ride $ride, string $status): int
    {
        if ($status === 'yes') {
            $goingYes = true;
        } else {
            $goingYes = false;
        }

        if ($status === 'maybe') {
            $goingMaybe = true;
        } else {
            $goingMaybe = false;
        }

        if ($status === 'no') {
            $goingNo = true;
        } else {
            $goingNo = false;
        }

        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p)')
            ->where($builder->expr()->eq('p.ride', ':ride'))
            ->setParameter('ride', $ride)
            ->andWhere($builder->expr()->eq('p.goingYes', ':goingYes'))
            ->setParameter('goingYes', $goingYes)
            ->andWhere($builder->expr()->eq('p.goingMaybe', ':goingMaybe'))
            ->setParameter('goingMaybe', $goingMaybe)
            ->andWhere($builder->expr()->eq('p.goingNo', ':goingNo'))
            ->setParameter('goingNo', $goingNo)
            ->setMaxResults(1);

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function countByUser(User $user): int
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->select('COUNT(p)')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->setParameter('user', $user)
            ->andWhere($builder->expr()->eq('p.goingYes', true));

        $query = $builder->getQuery();

        return (int) $query->getSingleScalarResult();
    }

    public function findByUser(User $user, bool $yes = false, bool $maybe = false, bool $no = false): array
    {
        $builder = $this->createQueryBuilder('p');

        $builder
            ->join('p.ride', 'r')
            ->where($builder->expr()->eq('p.user', ':user'))
            ->setParameter('user', $user)
            ->orderBy('r.dateTime', 'DESC');

        if ($yes) {
            $builder->andWhere($builder->expr()->eq('p.goingYes', true));
        }

        if ($maybe) {
            $builder->andWhere($builder->expr()->eq('p.goingMaybe', true));
        }

        if ($no) {
            $builder->andWhere($builder->expr()->eq('p.goingNo', true));
        }

        $query = $builder->getQuery();

        return $query->getResult();
    }
}

