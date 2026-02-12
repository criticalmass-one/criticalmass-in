<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\AbstractTimelineCollector;
use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use App\Criticalmass\Timeline\Item\ItemInterface;
use App\Criticalmass\Timeline\Serializer\TimelineItemSerializer;
use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;

class Timeline implements TimelineInterface
{
    protected array $collectorList = [];

    protected array $items = [];

    protected array $contentList = [];

    protected ?\DateTime $startDateTime = null;

    protected ?\DateTime $endDateTime = null;

    public function __construct(
        protected readonly ManagerRegistry $doctrine,
        protected readonly TimelineItemSerializer $serializer,
        protected readonly FeatureManagerInterface $featureManager
    ) {
    }

    public function addCollector(AbstractTimelineCollector $collector): TimelineInterface
    {
        if ($this->checkFeatureStatusForCollector($collector)) {
            array_push($this->collectorList, $collector);
        }

        return $this;
    }

    public function setDateRange(\DateTime $startDateTime, \DateTime $endDateTime): TimelineInterface
    {
        $this->startDateTime = $startDateTime;
        $this->endDateTime = $endDateTime;

        return $this;
    }

    public function execute(): TimelineInterface
    {
        $this->process();

        return $this;
    }

    protected function process(): Timeline
    {
        /** @var AbstractTimelineCollector $collector */
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

        $counter = [];

        /**
         * @var string $key
         * @var ItemInterface $item
         */
        foreach ($this->items as $key => $item) {
            if (!array_key_exists($item->getTabName(), $counter)) {
                $counter[$item->getTabName()] = 1;
            } else {
                ++$counter[$item->getTabName()];
            }

            if ($item->getDateTime() < $lastDateTime || $counter[$item->getTabName()] > self::MAX_ITEMS) {
                unset($this->items[$key]);
            }
        }

        return $this;
    }

    protected function createContent(): Timeline
    {
        /** @var ItemInterface $item */
        foreach ($this->items as $item) {
            $this->contentList[] = $this->serializer->serialize($item);
        }

        return $this;
    }

    public function getTimelineContentList(): array
    {
        return $this->contentList;
    }

    protected function checkFeatureStatusForCollector(TimelineCollectorInterface $timelineCollector): bool
    {
        $requiredFeatures = $timelineCollector->getRequiredFeatures();

        if (count($requiredFeatures) > 0) {
            foreach ($requiredFeatures as $requiredFeature) {
                if (!$this->featureManager->isActive($requiredFeature)) {
                    return false;
                }
            }
        }

        return true;
    }
}

