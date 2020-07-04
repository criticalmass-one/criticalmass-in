<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Entity\SocialNetworkProfile;

class QueryBuilder
{
    private function __construct()
    {

    }

    public static function build(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): string
    {
        $queryStringParts = [
            'tweet_mode' => 'extended',
            'screen_name' => Screenname::extractScreenname($socialNetworkProfile),
        ];

        if ($socialNetworkProfile->getAdditionalData() && $socialNetworkProfile->getAdditionalData()['lastTweetId']) {
            $queryStringParts['since_id'] = $socialNetworkProfile->getAdditionalData()['lastTweetId'];
        }

        if ($fetchInfo->hasFromDateTime()) {
            $queryStringParts['since'] = $fetchInfo
                ->getFromDateTime()
                ->format('Y-m-d');
        } elseif (!$fetchInfo->includeOldItems() && $socialNetworkProfile->getLastFetchSuccessDateTime()) {
            $queryStringParts['since'] = $socialNetworkProfile
                ->getLastFetchSuccessDateTime()
                ->format('Y-m-d');
        }

        if ($fetchInfo->hasUntilDatetime()) {
            $queryStringParts['until'] = $fetchInfo->getUntilDateTime()->format('Y-m-d');
        }

        return http_build_query($queryStringParts);
    }
}