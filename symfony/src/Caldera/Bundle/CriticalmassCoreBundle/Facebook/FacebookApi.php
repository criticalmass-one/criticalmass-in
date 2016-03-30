<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Facebook;

use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Facebook\Facebook;

abstract class FacebookApi
{
    /**
     * @var Facebook $facebook
     */
    protected $facebook;

    public function __construct($facebookAppId, $facebookAppSecret, $facebookDefaultToken)
    {
        $this->initFacebook(
            $facebookAppId,
            $facebookAppSecret,
            $facebookDefaultToken
        );
    }

    protected function initFacebook($facebookAppId, $facebookAppSecret, $facebookDefaultToken)
    {
        $this->facebook = new Facebook(
            [
                'app_id' => $facebookAppId,
                'app_secret' => $facebookAppSecret,
                'default_graph_version' => 'v2.5',
                'default_access_token' => $facebookDefaultToken
            ]
        );
    }

    protected function getCityPageId(City $city)
    {
        $facebook = $city->getFacebook();

        if (strpos($facebook, 'https://www.facebook.com/') == 0) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }
}