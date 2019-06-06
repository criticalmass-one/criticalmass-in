<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities\CriteriaBuilder;

use App\Criticalmass\OrderedEntities\OrderedEntityInterface;
use Doctrine\Common\Collections\Criteria;

class CriteriaBuilder implements CriteriaBuilderInterface
{
    public function build(OrderedEntityInterface $orderedEntity, string $direction): Criteria
    {
        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expr->lt('dateTime', $orderedEntity->getDateTime()))
            ->andWhere($expr->eq('city', $orderedEntity->getCity()));

        $criteria->orderBy(['dateTime' => 'asc']);

        return $criteria;
    }
}