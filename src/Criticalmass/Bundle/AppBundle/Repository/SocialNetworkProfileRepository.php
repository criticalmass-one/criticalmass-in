<?php

namespace Criticalmass\Bundle\AppBundle\Repository;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SocialNetworkProfileRepository extends EntityRepository
{
    protected function getProfileAbleQueryBuilder(): QueryBuilder
    {
        $builder = $this->createQueryBuilder('snp');

        $builder
            ->where($builder->expr()->eq('snp.enabled', ':enabled'))
            ->setParameter('enabled', true);

        return $builder;
    }

    public function findByCity(City $city): array
    {
        $queryBuilder = $this->getProfileAbleQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq('snp.city', ':city'))
            ->setParameter('city', $city);

        return $queryBuilder->getQuery()->getResult();
    }
}
