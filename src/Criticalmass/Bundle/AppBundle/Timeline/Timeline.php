<?php

namespace Criticalmass\Bundle\AppBundle\Timeline;

use AppBundle\Timeline\Collector\AbstractTimelineCollector;
use AppBundle\Timeline\Item\ItemInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\Templating\EngineInterface;

class Timeline
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var TwigEngine $templating */
    protected $templating;

    /** @var array $collectorList */
    protected $collectorList = [];

    /** @var array $items */
    protected $items = [];

    /** @var string $content */
    protected $content = '';

    /** @var \DateTime $startDateTime */
    protected $startDateTime = null;

    /** @var \DateTime $endDateTime */
    protected $endDateTime = null;

    public function __construct(RegistryInterface $doctrine, EngineInterface $templating)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
    }

    public function addCollector(AbstractTimelineCollector $collector): Timeline
    {
        array_push($this->collectorList, $collector);

        return $this;
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): Timeline
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function execute(): Timeline
    {
        $this->process();

        return $this;
    }

    protected function process(): Timeline
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

    protected function paginate(): Timeline
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

    protected function createContent(): Timeline
    {
        foreach ($this->items as $item) {
            $templateName = $this->templateNameForItem($item);

            $this->content .= $this->templating->render(
                'AppBundle:Timeline/Items:' . $templateName . '.html.twig',
                [
                    'item' => $item
                ]
            );
        }

        return $this;
    }

    protected function templateNameForItem(ItemInterface $item): string
    {
        $itemFullClassName = get_class($item);

        $itemClassNamespaces = explode('\\', $itemFullClassName);

        $itemClassName = array_pop($itemClassNamespaces);

        $templateName = lcfirst(str_replace('Item', '', $itemClassName));

        return $templateName;
    }

    public function getTimelineContent(): string
    {
        return $this->content;
    }
}

