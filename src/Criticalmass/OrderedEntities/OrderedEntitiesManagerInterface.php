<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities;

interface OrderedEntitiesManagerInterface
{
    public function getPrevious(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface;
    public function getNextEntity(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface;
}