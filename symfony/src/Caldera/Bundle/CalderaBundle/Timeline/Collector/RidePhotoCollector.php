<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Collector;

use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item\RidePhotoItem;

class RidePhotoCollector extends AbstractTimelineCollector
{
    protected function fetchEntities()
    {
        return $this->doctrine->getRepository('CalderaBundle:Photo')->findForTimelinePhotoCollector($this->startDateTime, $this->endDateTime);
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

            if (!array_key_exists($userKey, $groupedEntities) || !is_array($groupedEntities[$userKey])) {
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
                $item->setUsername($lastPhoto->getUser()->getUsername());
                $item->setRide($lastPhoto->getRide());
                $item->setCity($lastPhoto->getCity());
                $item->setRideTitle($lastPhoto->getRide()->getFancyTitle());
                $item->setDateTime($lastPhoto->getCreationDateTime());

                $this->addItem($item);
            }
        }
    }
}