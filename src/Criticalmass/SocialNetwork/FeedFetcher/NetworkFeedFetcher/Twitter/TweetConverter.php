<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter;

use App\Entity\SocialNetworkFeedItem;

class TweetConverter
{
    private function __construct()
    {

    }

    public static function convert(\stdClass $tweet): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();

        try {
            $permalink = sprintf('https://twitter.com/i/web/status/%s', $tweet->id);
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