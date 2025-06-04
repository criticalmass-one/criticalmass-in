<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Collector;

use App\Criticalmass\Timeline\Item\RidePhotoItem;
use App\Entity\Photo;

class RidePhotoCollector extends AbstractTimelineCollector
{
    protected string $entityClass = Photo::class;

    protected function groupEntities(array $photoEntities): array
    {
        $groupedEntities = [];

        /** @var Photo $photoEntity */
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

    protected function convertGroupedEntities(array $groupedEntities): AbstractTimelineCollector
    {
        foreach ($groupedEntities as $userGroup) {
            foreach ($userGroup as $rideGroup) {
                $photoCounter = count($rideGroup);

                $item = new RidePhotoItem();

                $item = $this->grapRandomFotos($rideGroup, $item);

                // take last photo to fetch $user and $ride and $dateTime
                $lastPhoto = array_pop($rideGroup);

                $item
                    ->setUser($lastPhoto->getUser())
                    ->setRide($lastPhoto->getRide())
                    ->setDateTime($lastPhoto->getCreationDateTime())
                    ->setCounter($photoCounter)
                    ->setRideEnabled($lastPhoto->getRide()->isEnabled());

                $this->addItem($item);
            }
        }

        return $this;
    }

    public function getRequiredFeatures(): array
    {
        return ['photos'];
    }

    protected function grapRandomFotos(array $rideGroup, RidePhotoItem $item, int $maxPhotos = 3): RidePhotoItem
    {
        $counter = count($rideGroup);

        $previewPhotoIds = array_rand($rideGroup, $counter < $maxPhotos ? $counter : $maxPhotos);

        if (is_array($previewPhotoIds)) {
            foreach ($previewPhotoIds as $previewPhotoId) {
                $item->addPreviewPhoto($rideGroup[$previewPhotoId]);
            }
        } else {
            $item->addPreviewPhoto($rideGroup[$previewPhotoIds]);
        }

        return $item;
    }
}
