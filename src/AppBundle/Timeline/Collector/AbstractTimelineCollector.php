<?php

namespace AppBundle\Timeline\Collector;

use AppBundle\Timeline\Item\ItemInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

abstract class AbstractTimelineCollector implements TimelineCollectorInterface
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var array $items */
    protected $items = [];

    /** @var \DateTime $startDateTime */
    protected $startDateTime = null;

    /** @var \DateTime $endDateTime */
    protected $endDateTime = null;

    public function __construct(Doctrine $doctrine)
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