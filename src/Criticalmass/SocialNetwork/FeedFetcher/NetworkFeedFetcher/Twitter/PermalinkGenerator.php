<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter;

use App\Entity\SocialNetworkProfile;

class PermalinkGenerator
{
    private function __construct()
    {

    }

    public static function generatePermalink(SocialNetworkProfile $socialNetworkProfile, \stdClass $tweet): string
    {
        $screenName = Screenname::extractScreenname($socialNetworkProfile);
        $tweetId = $tweet->id;

        return sprintf('https://twitter.com/%s/status/%d', $screenName, $tweetId);
    }
}