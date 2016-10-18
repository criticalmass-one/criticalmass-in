<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\ItemInterface;

abstract class AbstractTimelineCollector
{
    protected $doctrine;

    protected $items = [];

    protected $startDateTime = null;
    protected $endDateTime = null;

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;
    }

    public function execute()
    {
        $entities = $this->fetchEntities();
        $groupedEntities = $this->groupEntities($entities);
        $this->convertGroupedEntities($groupedEntities);
    }

    protected abstract function fetchEntities();

    protected abstract function groupEntities(array $entities);

    protected abstract function convertGroupedEntities(array $groupedEntities);

    public function getItems()
    {
        return $this->items;
    }

    protected function addItem(ItemInterface $item)
    {
        $dateTimeString = $item->getDateTime()->format('Y-m-d-H-i-s');

        $itemKey = $dateTimeString . '-' . $item->getUniqId();

        $this->items[$itemKey] = $item;
    }
}