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

    public function __construct($doctrine, $templating)
    {
        $this->doctrine = $doctrine;
        $this->templating = $templating;
    }

    public function addCollector(AbstractTimelineCollector $collector)
    {
        array_push($this->collectorList, $collector);
    }

    public function execute()
    {
        /**
         * @var AbstractTimelineCollector $collector
         */
        foreach ($this->collectorList as $collector) {
            $collector->execute();

            $this->items = array_merge($this->items, $collector->getItems());
        }

        krsort($this->items);

        $this->createContent();

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

