<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector\AbstractTimelineCollector;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\ItemInterface;

class Timeline
{
    protected $doctrine;
    protected $templating;

    protected $collectorList = [];
    protected $items = [];
    protected $content = '';

    protected $startDateTime = null;
    protected $endDateTime = null;

    public function __construct($doctrine, $templating)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
    }

    public function addCollector(AbstractTimelineCollector $collector)
    {
        array_push($this->collectorList, $collector);

        return $this;
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime)
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function execute()
    {
        $this->process();

        return $this;
    }

    protected function process()
    {
        /**
         * @var AbstractTimelineCollector $collector
         */
        foreach ($this->collectorList as $collector) {
            $collector->setDateRange($this->startDateTime, $this->endDateTime);

            $collector->execute();

            $this->items = array_merge($this->items, $collector->getItems());
        }

        krsort($this->items);

        $this->paginate();

        $this->createContent();

        return $this;
    }

    protected function paginate()
    {
        $lastDateTime = new \DateTime();
        $threeMonthDateInterval = new \DateInterval('P3M');
        $lastDateTime->sub($threeMonthDateInterval);

        $maxItems = 50;
        $counter = 0;

        foreach ($this->items as $key => $item) {
            ++$counter;

            if ($item->getDateTime() < $lastDateTime || $counter > $maxItems) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    protected function createContent()
    {
        foreach ($this->items as $item) {
            $templateName = $this->templateNameForItem($item);

            $this->content .= $this->templating->render(
                'CalderaCriticalmassSiteBundle:Timeline/Items:'.$templateName.'.html.twig',
                [
                    'item' => $item
                ]
            );
        }

        return $this;
    }

    protected function templateNameForItem(ItemInterface $item)
    {
        $itemFullClassName = get_class($item);

        $itemClassNamespaces = explode('\\', $itemFullClassName);

        $itemClassName = array_pop($itemClassNamespaces);

        $templateName = lcfirst(str_replace('Item', '', $itemClassName));

        return $templateName;
    }

    public function getTimelineContent()
    {
        return $this->content;
    }
}

