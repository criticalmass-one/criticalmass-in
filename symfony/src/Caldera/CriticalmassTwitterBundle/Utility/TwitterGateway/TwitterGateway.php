<?php

namespace Caldera\CriticalmassTwitterBundle\Utility\TwitterGateway;

use Abraham\TwitterOAuth\TwitterOAuth;
use Caldera\CriticalmassCoreBundle\Entity\Ride;

class TwitterGateway {
    protected $consumerKey;
    protected $consumerSecret;
    protected $accessToken;
    protected $accessTokenSecret;

    protected $toa;

    public function __construct($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->accessToken = $accessToken;
        $this->accessTokenSecret = $accessTokenSecret;

        $this->toa = new TwitterOAuth($this->consumerKey, $this->consumerSecret, $this->accessToken, $this->accessTokenSecret);
    }

    public function fetchTweetsForRide(Ride $ride)
    {
        $query = array(
            'q' => '#'.$ride->getHashtag()
        );

        $results = $this->toa->get('search/tweets', $query);

        return $results;
    }
}