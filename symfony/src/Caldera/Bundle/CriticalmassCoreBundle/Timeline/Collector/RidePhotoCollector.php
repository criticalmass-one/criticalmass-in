<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RidePhotoItem;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;

class RidePhotoCollector extends AbstractTimelineCollector
{
    public function execute()
    {
        $photoEntities = $this->fetchEntities();
        $sortedEntities = $this->groupEntities($photoEntities);
        $this->convertGroupedEntities($sortedEntities);
    }

    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Photo')->findForTimelinePhotoCollector();
    }

    protected function groupEntities(array $photoEntities)
    {
        $groupedEntities = [];

        /**
         * @var Photo $photoEntity
         */
        foreach ($photoEntities as $photoEntity) {
            $userKey = $photoEntity->getUser()->getId();
            $rideKey = $photoEntity->getRide()->getId();
            $photoKey = $photoEntity->getId();

            if (!array_key_exists($userKey, $groupedEntities) or !is_array($groupedEntities[$userKey])) {
                $groupedEntities[$userKey] = [];
            }

            $groupedEntities[$userKey][$rideKey][$photoKey] = $photoEntity;
        }

        return $groupedEntities;
    }

    protected function convertGroupedEntities(array $groupedEntities)
    {
        foreach ($groupedEntities as $userGroup) {
            foreach ($userGroup as $rideGroup) {
                $item = new RidePhotoItem();
                $item->setCounter(count($rideGroup));

                // grab a random photo as preview
                $previewPhotoId = array_rand($rideGroup);
                $item->setPreviewPhoto($rideGroup[$previewPhotoId]);
                
                // take last photo to fetch $user and $ride and $dateTime
                $lastPhoto = array_pop($rideGroup);
                $item->setUser($lastPhoto->getUser());
                $item->setRide($lastPhoto->getRide());

                array_push($this->items, $item);
            }
        }
    }
}