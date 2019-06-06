<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities;

use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrderedEntitiesManager implements OrderedEntitiesManagerInterface
{
    /** @var RegistryInterface $registry  */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getPrevious(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $orderedEntity;
    }

    public function getNextEntity(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $orderedEntity;
    }

    protected function findEntity(OrderedEntityInterface $orderedEntity, string $direction): ?OrderedEntityInterface
    {
        $className = get_class($orderedEntity);

        $criteria = new Criteria();

        return $this->registry->getRepository($className)->findOneBy($criteria);
    }
}