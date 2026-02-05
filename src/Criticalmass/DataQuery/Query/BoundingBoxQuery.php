<?php declare(strict_types=1);

namespace App\Criticalmass\DataQuery\Query;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\Constraints as Constraints;

class BoundingBoxQuery
{
    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -90, max: 90)]
    protected ?float $northLatitude = null;

    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -90, max: 90)]
    protected ?float $southLatitude = null;

    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -180, max: 180)]
    protected ?float $eastLongitude = null;

    #[Constraints\NotNull]
    #[Constraints\Type('float')]
    #[Constraints\Range(min: -180, max: 180)]
    protected ?float $westLongitude = null;

    public function setNorthLatitude(float $northLatitude): BoundingBoxQuery
    {
        $this->northLatitude = $northLatitude;
        return $this;
    }

    public function setSouthLatitude(float $southLatitude): BoundingBoxQuery
    {
        $this->southLatitude = $southLatitude;
        return $this;
    }

    public function setEastLongitude(float $eastLongitude): BoundingBoxQuery
    {
        $this->eastLongitude = $eastLongitude;
        return $this;
    }

    public function setWestLongitude(float $westLongitude): BoundingBoxQuery
    {
        $this->westLongitude = $westLongitude;
        return $this;
    }

    public function getNorthLatitude(): float
    {
        return $this->northLatitude;
    }

    public function getSouthLatitude(): float
    {
        return $this->southLatitude;
    }

    public function getEastLongitude(): float
    {
        return $this->eastLongitude;
    }

    public function getWestLongitude(): float
    {
        return $this->westLongitude;
    }

    public function hasNorthLatitude(): bool
    {
        return $this->northLatitude !== null;
    }

    public function hasSouthLatitude(): bool
    {
        return $this->southLatitude !== null;
    }

    public function hasEastLongitude(): bool
    {
        return $this->eastLongitude !== null;
    }

    public function hasWestLongitude(): bool
    {
        return $this->westLongitude !== null;
    }

    public function createElasticQuery(): \Elastica\Query\AbstractQuery
    {
        $geoQuery = new \Elastica\Query\GeoBoundingBox('pin', [
            [$this->westLongitude, $this->northLatitude],
            [$this->eastLongitude, $this->southLatitude],
        ]);

        return $geoQuery;
    }

    public function createOrmQuery(QueryBuilder $queryBuilder): QueryBuilder
    {
        $alias = $queryBuilder->getRootAliases()[0];
        $expr = $queryBuilder->expr();

        $queryBuilder
            ->andWhere($expr->andX(
                $expr->gte(sprintf('%s.latitude', $alias), ':southLatitude'),
                $expr->lte(sprintf('%s.latitude', $alias), ':northLatitude')
            ))
            ->andWhere($expr->andX(
                $expr->gte(sprintf('%s.longitude', $alias), ':westLongitude'),
                $expr->lte(sprintf('%s.longitude', $alias), ':eastLongitude')
            ))
            ->setParameter('southLatitude', $this->southLatitude)
            ->setParameter('northLatitude', $this->northLatitude)
            ->setParameter('westLongitude', $this->westLongitude)
            ->setParameter('eastLongitude', $this->eastLongitude)
        ;

        return $queryBuilder;
    }
}
