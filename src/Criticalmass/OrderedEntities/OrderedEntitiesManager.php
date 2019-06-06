<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\LazyCriteriaCollection;
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
        return $this->findEntity($orderedEntity, 'desc');
    }

    public function getNextEntity(OrderedEntityInterface $orderedEntity): ?OrderedEntityInterface
    {
        return $this->findEntity($orderedEntity, 'asc');
    }

    protected function findEntity(OrderedEntityInterface $orderedEntity, string $direction): ?OrderedEntityInterface
    {
        $className = get_class($orderedEntity);

        $expr = Criteria::expr();
        $criteria = Criteria::create();
        $criteria->where($expr->lt('dateTime', $orderedEntity->getDateTime()))
            ->andWhere($expr->eq('city', $orderedEntity->getCity()));

        $criteria->orderBy(['dateTime' => 'asc']);

        $resultList = $this->registry->getRepository($className)
            ->matching($criteria)
            ->getValues();

        return array_pop($resultList);
    }
}