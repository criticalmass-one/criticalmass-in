<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\DataQueryManager;

use App\Criticalmass\DataQuery\RequestParameterList\RequestParameterList;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class DataQueryManager implements DataQueryManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function query(RequestParameterList $requestParameterList, string $entityFqcn): array
    {
        $repository = $this->entityManager->getRepository($entityFqcn);
        $queryBuilder = $repository->createQueryBuilder('e');

        $this->applyFilters($queryBuilder, $requestParameterList, $entityFqcn);
        $this->applyOrdering($queryBuilder, $requestParameterList);
        $this->applyLimits($queryBuilder, $requestParameterList);

        return $queryBuilder->getQuery()->getResult();
    }

    private function applyFilters(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList, string $entityFqcn): void
    {
        // Apply enabled filter for entities that have it
        if ($requestParameterList->has('isEnabled')) {
            $queryBuilder
                ->andWhere('e.enabled = :enabled')
                ->setParameter('enabled', $requestParameterList->get('isEnabled') === 'true');
        }

        // Apply year/month/day filters
        if ($requestParameterList->has('year')) {
            $year = (int) $requestParameterList->get('year');
            $startDate = new \DateTime(sprintf('%d-01-01', $year));
            $endDate = new \DateTime(sprintf('%d-01-01', $year + 1));

            if ($requestParameterList->has('month')) {
                $month = (int) $requestParameterList->get('month');
                $startDate = new \DateTime(sprintf('%d-%02d-01', $year, $month));
                $endDate = (clone $startDate)->modify('+1 month');

                if ($requestParameterList->has('day')) {
                    $day = (int) $requestParameterList->get('day');
                    $startDate = new \DateTime(sprintf('%d-%02d-%02d', $year, $month, $day));
                    $endDate = (clone $startDate)->modify('+1 day');
                }
            }

            $queryBuilder
                ->andWhere('e.dateTime >= :startDate')
                ->andWhere('e.dateTime < :endDate')
                ->setParameter('startDate', $startDate)
                ->setParameter('endDate', $endDate);
        }
    }

    private function applyOrdering(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
        if ($requestParameterList->has('orderBy')) {
            $orderBy = $requestParameterList->get('orderBy');
            $orderDirection = $requestParameterList->has('orderDirection')
                ? strtoupper($requestParameterList->get('orderDirection'))
                : 'ASC';

            if (!in_array($orderDirection, ['ASC', 'DESC'], true)) {
                $orderDirection = 'ASC';
            }

            $queryBuilder->orderBy(sprintf('e.%s', $orderBy), $orderDirection);
        }
    }

    private function applyLimits(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
        $size = $requestParameterList->has('size') ? (int) $requestParameterList->get('size') : 10;
        $queryBuilder->setMaxResults($size);

        if ($requestParameterList->has('offset')) {
            $queryBuilder->setFirstResult((int) $requestParameterList->get('offset'));
        }
    }
}
