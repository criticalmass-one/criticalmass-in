<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities;

class OrderedEntitiesManager
{
    public function getPrevious(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $orderedEntity;
    }

    public function getNextEntity(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $orderedEntity;
    }
}