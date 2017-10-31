<?php

namespace AppBundle\Facebook;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use Facebook\Facebook;

abstract class AbstractFacebookApi
{
    /** @var Facebook $facebook */
    protected $facebook;

    public function __construct(string $facebookAppId, string $facebookAppSecret, string $facebookDefaultToken)
    {
        $this->initFacebook(
            $facebookAppId,
            $facebookAppSecret,
            $facebookDefaultToken
        );
    }

    protected function initFacebook(string $facebookAppId, string $facebookAppSecret, string $facebookDefaultToken): AbstractFacebookApi
    {
        $this->facebook = new Facebook(
            [
                'app_id' => $facebookAppId,
                'app_secret' => $facebookAppSecret,
                'default_graph_version' => 'v2.5',
                'default_access_token' => $facebookDefaultToken,
            ]
        );

        return $this;
    }

    protected function getRideEventId(Ride $ride): ?string
    {
        $facebook = $ride->getFacebook();

        if (0 === strpos($facebook, 'https://www.facebook.com/')) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $eventId = array_pop($parts);

            return $eventId;
        }

        return null;
    }

    protected function getCityPageId(City $city): ?string
    {
        $facebook = $city->getFacebook();

        if (0 === strpos($facebook, 'https://www.facebook.com/')) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }
}