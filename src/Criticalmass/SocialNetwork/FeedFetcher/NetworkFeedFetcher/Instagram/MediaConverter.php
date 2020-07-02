<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Instagram;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use InstagramScraper\Model\Media;

class MediaConverter
{
    private function __construct()
    {

    }

    public static function convert(SocialNetworkProfile $socialNetworkProfile, Media $media): ?SocialNetworkFeedItem
    {
        $item = new SocialNetworkFeedItem();
        $item
            ->setSocialNetworkProfile($socialNetworkProfile)
            ->setRaw(json_encode($media))
            ->setDateTime(new \DateTime(sprintf('@%d', $media->getCreatedTime())))
            ->setText($media->getCaption())
            ->setPermalink($media->getLink())
            ->setUniqueIdentifier($media->getLink());

        return $item;
    }
}