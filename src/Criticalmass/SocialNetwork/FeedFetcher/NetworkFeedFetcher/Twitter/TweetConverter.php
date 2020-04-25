<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;

class TweetConverter
{
    private function __construct()
    {

    }

    public static function convert(SocialNetworkProfile $socialNetworkProfile, \stdClass $tweet): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();
        $feedItem->setSocialNetworkProfile($socialNetworkProfile);

        try {
            $permalink = PermalinkGenerator::generatePermalink($socialNetworkProfile, $tweet);

            $text = $tweet->full_text;
            $dateTime = new \DateTime($tweet->created_at);

            if ($permalink && $text && $dateTime) {
                $feedItem
                    ->setUniqueIdentifier($permalink)
                    ->setPermalink($permalink)
                    ->setText($text)
                    ->setDateTime($dateTime);

                return $feedItem;
            }

            return $feedItem;
        } catch (\Exception $e) {
            return null;
        }
    }
}