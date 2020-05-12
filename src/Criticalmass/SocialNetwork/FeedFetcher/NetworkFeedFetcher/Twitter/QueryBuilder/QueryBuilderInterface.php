<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter\QueryBuilder;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Entity\SocialNetworkProfile;

interface QueryBuilderInterface
{
    public static function build(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): string;
}
