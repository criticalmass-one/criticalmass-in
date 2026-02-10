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
        $this->applyOrdering($queryBuilder, $requestParameterList, $entityFqcn);
        $this->applyLimits($queryBuilder, $requestParameterList);

        return $queryBuilder->getQuery()->getResult();
    }

    private function applyFilters(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList, string $entityFqcn): void
    {
        $metadata = $this->entityManager->getClassMetadata($entityFqcn);

        if ($metadata->hasField('deleted')) {
            $queryBuilder
                ->andWhere('e.deleted = :defaultDeleted')
                ->setParameter('defaultDeleted', false);
        }

        if ($metadata->hasField('enabled')) {
            if ($requestParameterList->has('isEnabled')) {
                $queryBuilder
                    ->andWhere('e.enabled = :enabled')
                    ->setParameter('enabled', $requestParameterList->get('isEnabled') === 'true');
            } else {
                $queryBuilder
                    ->andWhere('e.enabled = :enabled')
                    ->setParameter('enabled', true);
            }
        }

        if ($requestParameterList->has('year')) {
            $this->applyDateFilters($queryBuilder, $requestParameterList);
        }

        if ($requestParameterList->has('name')) {
            $this->applyNameFilter($queryBuilder, $requestParameterList, $entityFqcn);
        }

        if ($this->hasBoundingBoxParams($requestParameterList)) {
            $this->applyBoundingBoxFilter($queryBuilder, $requestParameterList);
        }

        if ($this->hasRadiusParams($requestParameterList)) {
            $this->applyRadiusFilter($queryBuilder, $requestParameterList);
        }

        if ($requestParameterList->has('startValue') && $requestParameterList->has('orderBy')) {
            $this->applyStartValueFilter($queryBuilder, $requestParameterList, $entityFqcn);
        }
    }

    private function applyDateFilters(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
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

    private function applyNameFilter(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList, string $entityFqcn): void
    {
        $name = $requestParameterList->get('name');

        if ($entityFqcn === \App\Entity\Ride::class) {
            $queryBuilder
                ->join('e.city', 'c')
                ->andWhere($queryBuilder->expr()->eq('c.city', ':city'));
        } elseif ($entityFqcn === \App\Entity\City::class) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('e.city', ':city'));
        }

        $queryBuilder->setParameter('city', $name);
    }

    private function hasBoundingBoxParams(RequestParameterList $requestParameterList): bool
    {
        return $requestParameterList->has('bbNorthLatitude')
            && $requestParameterList->has('bbSouthLatitude')
            && $requestParameterList->has('bbEastLongitude')
            && $requestParameterList->has('bbWestLongitude');
    }

    private function applyBoundingBoxFilter(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->andWhere($expr->gte('e.latitude', ':southLatitude'))
            ->andWhere($expr->lte('e.latitude', ':northLatitude'))
            ->andWhere($expr->gte('e.longitude', ':westLongitude'))
            ->andWhere($expr->lte('e.longitude', ':eastLongitude'))
            ->setParameter('southLatitude', (float) $requestParameterList->get('bbSouthLatitude'))
            ->setParameter('northLatitude', (float) $requestParameterList->get('bbNorthLatitude'))
            ->setParameter('westLongitude', (float) $requestParameterList->get('bbWestLongitude'))
            ->setParameter('eastLongitude', (float) $requestParameterList->get('bbEastLongitude'));
    }

    private function hasRadiusParams(RequestParameterList $requestParameterList): bool
    {
        return $requestParameterList->has('centerLatitude')
            && $requestParameterList->has('centerLongitude');
    }

    private function applyRadiusFilter(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
        $centerLat = (float) $requestParameterList->get('centerLatitude');
        $centerLon = (float) $requestParameterList->get('centerLongitude');

        if ($requestParameterList->has('radius')) {
            $radius = (float) $requestParameterList->get('radius');

            $latDelta = $radius / 111.0;
            $lonDelta = $radius / (111.0 * cos(deg2rad($centerLat)));

            $queryBuilder
                ->andWhere('e.latitude BETWEEN :minLat AND :maxLat')
                ->andWhere('e.longitude BETWEEN :minLon AND :maxLon')
                ->setParameter('minLat', $centerLat - $latDelta)
                ->setParameter('maxLat', $centerLat + $latDelta)
                ->setParameter('minLon', $centerLon - $lonDelta)
                ->setParameter('maxLon', $centerLon + $lonDelta);
        }
    }

    private function applyStartValueFilter(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList, string $entityFqcn): void
    {
        $orderBy = $requestParameterList->get('orderBy');
        $startValue = $requestParameterList->get('startValue');
        $orderDirection = strtoupper($requestParameterList->get('orderDirection') ?? 'ASC');

        if (!$this->isValidEntityField($entityFqcn, $orderBy)) {
            return;
        }

        if ($orderDirection === 'DESC') {
            $queryBuilder
                ->andWhere(sprintf('e.%s <= :startValue', $orderBy))
                ->setParameter('startValue', $startValue);
        } else {
            $queryBuilder
                ->andWhere(sprintf('e.%s >= :startValue', $orderBy))
                ->setParameter('startValue', $startValue);
        }
    }

    private function applyOrdering(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList, string $entityFqcn): void
    {
        if ($requestParameterList->has('distanceOrderDirection') && $this->hasRadiusParams($requestParameterList)) {
            $this->applyDistanceOrdering($queryBuilder, $requestParameterList);
            return;
        }

        if ($requestParameterList->has('orderBy')) {
            $orderBy = $requestParameterList->get('orderBy');

            if (!$this->isValidEntityField($entityFqcn, $orderBy)) {
                return;
            }

            $orderDirection = $requestParameterList->has('orderDirection')
                ? strtoupper($requestParameterList->get('orderDirection'))
                : 'ASC';

            if (!in_array($orderDirection, ['ASC', 'DESC'], true)) {
                $orderDirection = 'ASC';
            }

            $queryBuilder->orderBy(sprintf('e.%s', $orderBy), $orderDirection);
        }
    }

    private function applyDistanceOrdering(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
        $centerLat = (float) $requestParameterList->get('centerLatitude');
        $centerLon = (float) $requestParameterList->get('centerLongitude');
        $direction = strtoupper($requestParameterList->get('distanceOrderDirection'));

        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            $direction = 'ASC';
        }

        $distanceExpr = sprintf(
            '((%f - e.latitude) * (%f - e.latitude) + (%f - e.longitude) * (%f - e.longitude))',
            $centerLat,
            $centerLat,
            $centerLon,
            $centerLon
        );

        $queryBuilder->addSelect(sprintf('%s AS HIDDEN distance', $distanceExpr));
        $queryBuilder->orderBy('distance', $direction);
    }

    private function applyLimits(QueryBuilder $queryBuilder, RequestParameterList $requestParameterList): void
    {
        $size = 10;

        if ($requestParameterList->has('size')) {
            $sizeValue = $requestParameterList->get('size');

            if (!is_numeric($sizeValue)) {
                throw new \InvalidArgumentException(sprintf('Invalid size parameter: %s', $sizeValue));
            }

            $parsedSize = (int) $sizeValue;

            if ($parsedSize > 0) {
                $size = $parsedSize;
            }
        }

        $queryBuilder->setMaxResults($size);

        if ($requestParameterList->has('offset')) {
            $queryBuilder->setFirstResult((int) $requestParameterList->get('offset'));
        }
    }

    private function isValidEntityField(string $entityFqcn, string $fieldName): bool
    {
        $metadata = $this->entityManager->getClassMetadata($entityFqcn);

        return $metadata->hasField($fieldName) || $metadata->hasAssociation($fieldName);
    }
}
