<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter\QueryBuilder;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter\Screenname;
use App\Entity\SocialNetworkProfile;

class SearchQueryBuilder implements QueryBuilderInterface
{
    private function __construct()
    {

    }

    public static function build(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): string
    {
        $parameters = [
            'from' => Screenname::extractScreenname($socialNetworkProfile),
        ];

        if ($fetchInfo->hasFromDateTime()) {
            $parameters['since'] = $fetchInfo
                ->getFromDateTime()
                ->format('Y-m-d');
        } elseif ($fetchInfo->skipOldItems() && $socialNetworkProfile->getLastFetchSuccessDateTime()) {
            $parameters['since'] = $socialNetworkProfile
                ->getLastFetchSuccessDateTime()->sub(new \DateInterval('P1D'))
                ->format('Y-m-d');
        }

        if ($fetchInfo->hasUntilDatetime()) {
            $parameters['until'] = $fetchInfo->getUntilDateTime()->format('Y-m-d');
        }

        array_walk($parameters, function (string &$value, string $key): void {
            $value = sprintf('%s:%s', $key, $value);
        });

        $queryParameters = [
            'tweet_mode' => 'extended',
            'q' => join(' ', $parameters),
        ];

        return http_build_query($queryParameters);
    }
}