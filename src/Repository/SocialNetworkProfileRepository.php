<?php declare(strict_types=1);

namespace App\Repository;

use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Entity\City;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class SocialNetworkProfileRepository extends EntityRepository
{
    protected function getProfileAbleQueryBuilder(bool $enabled = true): QueryBuilder
    {
        $builder = $this->createQueryBuilder('snp');

        if ($enabled) {
            $builder
                ->where($builder->expr()->eq('snp.enabled', ':enabled'))
                ->setParameter('enabled', $enabled);
        }

        return $builder;
    }

    public function findByProperties(string $networkIdentifier = null, bool $autoFetch = null, City $city = null): array
    {
        $builder = $this->createQueryBuilder('snp');

        $builder
            ->where($builder->expr()->eq('snp.enabled', ':enabled'))
            ->setParameter('enabled', true);

        if ($networkIdentifier) {
            $builder
                ->andWhere($builder->expr()->eq('snp.network', ':network'))
                ->setParameter('network', $networkIdentifier);
        }

        if ($autoFetch) {
            $builder
                ->andWhere($builder->expr()->eq('snp.autoFetch', ':autoFetch'))
                ->setParameter('autoFetch', $autoFetch);
        }

        if ($city) {
            $builder
                ->andWhere($builder->expr()->eq('snp.city', ':city'))
                ->setParameter('city', $city);
        }

        $builder->orderBy('snp.createdAt');

        return $builder->getQuery()->getResult();
    }

    public function findByProfileable(SocialNetworkProfileAble $profileAble): array
    {
        $reflection = new \ReflectionClass($profileAble);
        $lcEntityClassname = lcfirst($reflection->getShortName());

        $joinColumnName = sprintf('snp.%s', $lcEntityClassname);

        $queryBuilder = $this->getProfileAbleQueryBuilder();

        $queryBuilder
            ->andWhere($queryBuilder->expr()->eq($joinColumnName, ':profileAble'))
            ->setParameter('profileAble', $profileAble);

        return $queryBuilder->getQuery()->getResult();
    }

    public function findByFetchInfo(FetchInfo $fetchInfo): array
    {
        $queryBuilder = $this->createQueryBuilder('snp');

        $queryBuilder
            ->where($queryBuilder->expr()->eq('snp.autoFetch', ':autoFetch'))
            ->setParameter('autoFetch', true);

        if ($fetchInfo->hasNetworkList()) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('snp.network', $fetchInfo->getNetworkList()));
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param string $method
     * @param array $arguments
     */
    public function __call($method, $arguments): array
    {
        $methodPrefix = 'findBy';
        $entityNamespace = 'App\\Entity';

        if (0 === strpos($method, $methodPrefix)) {
            $entityClassname = substr($method, 6);

            $fqcn = sprintf('%s\\%s', $entityNamespace, $entityClassname);
            $class = new $fqcn;

            if ($class instanceof SocialNetworkProfileAble) {
                return $this->findByProfileable($arguments[0]);
            }
        }

        return parent::__call($method, $arguments);
    }
}
