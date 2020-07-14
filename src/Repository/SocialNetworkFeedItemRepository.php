<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SocialNetworkFeedItemRepository extends EntityRepository
{
    public function findForTimelineSocialNetworkFeedItemCollector(\DateTime $startDateTime = null, \DateTime $endDateTime = null, int $limit = null): array
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

    protected function createDefaultQueryBuilder(City $city = null): QueryBuilder
    {
        $qb = $this->createQueryBuilder('snfi');

        $qb
            ->join('snfi.socialNetworkProfile', 'snp')
            ->andWhere($qb->expr()->eq('snfi.deleted', ':deleted'))
            ->andWhere($qb->expr()->eq('snfi.hidden', ':hidden'))
            ->andWhere($qb->expr()->eq('snp.enabled', ':enabled'))
            ->setParameter('deleted', false)
            ->setParameter('hidden', false)
            ->setParameter('enabled', true);

        if ($city) {
            $qb
                ->andWhere($qb->expr()->eq('snp.city', ':city'))
                ->setParameter('city', $city);
        }

        return $qb;
    }

    public function findByCity(City $city, string $orderDirection = 'DESC'): array
    {
        $qb = $this->createDefaultQueryBuilder($city);
        $qb->orderBy('snfi.dateTime', $orderDirection);

        return $qb->getQuery()->getResult();
    }

    public function findByCityAndProperties(City $city, string $uniqueIdentifier = null, string $networkIdentifier = null, string $orderDirection = 'DESC'): array
    {
        $qb = $this->createDefaultQueryBuilder($city);
        $qb->orderBy('snfi.dateTime', $orderDirection);

        if ($uniqueIdentifier) {
            $qb
                ->andWhere($qb->expr()->eq('snfi.uniqueIdentifier', ':uniqueIdentifier'))
                ->setParameter('uniqueIdentifier', $uniqueIdentifier);
        }

        if ($networkIdentifier) {
            $qb
                ->andWhere($qb->expr()->eq('snp.network', ':networkIdentifier'))
                ->setParameter('networkIdentifier', $networkIdentifier);
        }

        return $qb->getQuery()->getResult();
    }
}
