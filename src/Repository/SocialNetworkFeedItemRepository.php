<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;

class SocialNetworkFeedItemRepository extends EntityRepository
{
    public function findForTimelineSocialNetworkFeedItemCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, $limit = null): array
    {
        $builder = $this->createQueryBuilder('fi');

        if ($startDateTime) {
            $builder
                ->andWhere($builder->expr()->gte('fi.dateTime', ':startDateTime'))
                ->setParameter('startDateTime', $startDateTime);
        }

        if ($endDateTime) {
            $builder
                ->andWhere($builder->expr()->lte('fi.dateTime', ':endDateTime'))
                ->setParameter('endDateTime', $endDateTime);
        }

        $builder
            ->andWhere($builder->expr()->eq('fi.hidden', ':hidden'))
            ->andWhere($builder->expr()->eq('fi.deleted', ':deleted'))
            ->setParameter('hidden', false)
            ->setParameter('deleted', false);

        if ($limit) {
            $builder->setMaxResults($limit);
        }

        $builder->addOrderBy('fi.dateTime', 'DESC');

        $query = $builder->getQuery();

        $result = $query->getResult();

        return $result;
    }

    public function findByCity(City $city, $orderDirection = 'DESC'): array
    {
        $qb = $this->createQueryBuilder('snfi');

        $qb
            ->join('snfi.socialNetworkProfile', 'snp')
            ->where($qb->expr()->eq('snp.city', ':city'))
            ->andWhere($qb->expr()->eq('snfi.deleted', ':deleted'))
            ->andWhere($qb->expr()->eq('snfi.hidden', ':hidden'))
            ->andWhere($qb->expr()->eq('snp.enabled', ':enabled'))
            ->setParameter('city', $city)
            ->setParameter('deleted', false)
            ->setParameter('hidden', false)
            ->setParameter('enabled', true)
            ->orderBy('snfi.dateTime', $orderDirection);

        return $qb->getQuery()->getResult();
    }
}
