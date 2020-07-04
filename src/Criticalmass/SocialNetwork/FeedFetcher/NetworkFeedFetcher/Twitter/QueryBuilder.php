
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
        } elseif ($fetchInfo->skipOldItems() && $socialNetworkProfile->getLastFetchSuccessDateTime()) {
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