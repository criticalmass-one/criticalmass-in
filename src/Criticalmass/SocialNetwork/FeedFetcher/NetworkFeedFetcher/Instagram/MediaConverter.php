<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Instagram;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use InstagramScraper\Model\Media;
use JMS\Serializer\SerializerBuilder;

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
            ->setRaw(self::serializeRawMedia($media))
            ->setDateTime(new \DateTime(sprintf('@%d', $media->getCreatedTime())))
            ->setText($media->getCaption())
            ->setPermalink($media->getLink())
            ->setUniqueIdentifier($media->getLink());

        return $item;
    }

    protected static function serializeRawMedia(Media $media): string
    {
        $serializer = SerializerBuilder::create()->build();

        return $serializer->serialize($media, 'json');
    }
}