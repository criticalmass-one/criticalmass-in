<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\RideTrackItem;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RideTrackCollector extends AbstractTimelineCollector
{
    protected string $entityClass = Track::class;

    public function __construct(
        private readonly Security $security,
        ManagerRegistry $managerRegistry
    )
    {
        parent::__construct($managerRegistry);
    }

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        /* as we cannot change to another repository method in the timeline creation process, we will sort out some tracks here */
        /** @var Track $trackEntity */
        foreach ($groupedEntities as $trackEntity) {
            if (!$this->security->isGranted('publicView', $trackEntity)) {
                continue;
            }

            $item = new RideTrackItem();

            $item
                ->setUser($trackEntity->getUser())
                ->setRide($trackEntity->getRide())
                ->setTrack($trackEntity)
                ->setRideTitle($trackEntity->getRide()->getTitle())
                ->setDistance($trackEntity->getDistance())
                ->setDuration($trackEntity->getDurationInSeconds())
                ->setPolyline($trackEntity->getPolyline())
                ->setPolylineColor('rgb(' . $trackEntity->getUser()->getColorRed() . ', ' . $trackEntity->getUser()->getColorGreen() . ', ' . $trackEntity->getUser()->getColorBlue() . ')')
                ->setDateTime($trackEntity->getCreationDateTime())
                ->setRideEnabled($trackEntity->getRide()->isEnabled());

            $this->addItem($item);
        }

        return $this;
    }
}
