<?php declare(strict_types=1);

namespace App\Criticalmass\OrderedEntities;

use App\Criticalmass\OrderedEntities\CriteriaBuilder\CriteriaBuilderInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrderedEntitiesManager implements OrderedEntitiesManagerInterface
{
    /** @var RegistryInterface $registry  */
    protected $registry;

    /** @var CriteriaBuilderInterface $criteriaBuilder */
    protected $criteriaBuilder;

    public function __construct(RegistryInterface $registry, CriteriaBuilderInterface $criteriaBuilder)
    {
        $this->registry = $registry;
        $this->criteriaBuilder = $criteriaBuilder;
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

        $criteria = $this->criteriaBuilder->build($orderedEntity, $direction);

        $resultList = $this->registry->getRepository($className)
            ->matching($criteria)
            ->getValues();

        return array_pop($resultList);
    }
}