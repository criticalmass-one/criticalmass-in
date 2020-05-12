<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter\QueryBuilder;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter\Screenname;
use App\Entity\SocialNetworkProfile;

class HttpQueryBuilder implements QueryBuilderInterface
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

        if ($fetchInfo->hasFromDateTime()) {
            $queryStringParts['since'] = $fetchInfo
                ->getFromDateTime()
                ->format('Y-m-d');
        } elseif ($fetchInfo->skipOldItems() && $socialNetworkProfile->getLastFetchSuccessDateTime()) {
            $queryStringParts['since'] = $socialNetworkProfile
                ->getLastFetchSuccessDateTime()->sub(new \DateInterval('P1D'))
                ->format('Y-m-d');
        }

        if ($fetchInfo->hasUntilDatetime()) {
            $queryStringParts['until'] = $fetchInfo->getUntilDateTime()->format('Y-m-d');
        }

        return http_build_query($queryStringParts);
    }
}