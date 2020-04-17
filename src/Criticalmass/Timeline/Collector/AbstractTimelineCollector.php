<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\ItemInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractTimelineCollector implements TimelineCollectorInterface
{
    /** @var string $entityClass */
    protected $entityClass;

    /** @var ManagerRegistry $doctrine */
    protected $doctrine;

    /** @var array $items */
    protected $items = [];

    /** @var \DateTime $startDateTime */
    protected $startDateTime = null;

    /** @var \DateTime $endDateTime */
    protected $endDateTime = null;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineCollectorInterface
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function execute(): TimelineCollectorInterface
    {
        $entities = $this->fetchEntities();
        $groupedEntities = $this->groupEntities($entities);
        $this->convertGroupedEntities($groupedEntities);

        return $this;
    }

    protected function fetchEntities(): array
    {
        $tmp = explode('\\', get_class($this));
        $className = array_pop($tmp);
        $methodName = sprintf('findForTimeline%s', $className);

        return $this->doctrine->getRepository($this->entityClass)->$methodName($this->startDateTime,
            $this->endDateTime);
    }

    protected function groupEntities(array $entities): array
    {
        return $entities;
    }

    protected abstract function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector;

    public function getItems(): array
    {
        return $this->items;
    }

    protected function addItem(ItemInterface $item): AbstractTimelineCollector
    {
        $dateTimeString = $item->getDateTime()->format('Y-m-d-H-i-s');

        $itemKey = $dateTimeString . '-' . $item->getUniqId();

        $this->items[$itemKey] = $item;

        return $this;
    }

    public function getRequiredFeatures(): array
    {
        return [];
    }
}
