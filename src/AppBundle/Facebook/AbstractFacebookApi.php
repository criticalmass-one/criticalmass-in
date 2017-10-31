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
        return $this->getIdFromUrl($ride->getFacebook());
    }

    protected function getCityPageId(City $city): ?string
    {
        return $this->getIdFromUrl($city->getFacebook());
    }

    protected function getIdFromUrl(string $url): ?string
    {
        preg_match_all(
            '/^(http\:\/\/|https\:\/\/)?(?:www\.)?facebook\.com\/(?:(?:\w\.)*#!\/)?(?:pages\/)?(?:[\w\-\.]*\/)*([\w\-\.]*)/',
            $url,
            $matches
        );

        if (3 === count($matches)) {
            $pageId = array_pop($matches[2]);

            return $pageId;
        }

        return null;
    }
}