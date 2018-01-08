<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook\Api;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Facebook\Facebook;

abstract class FacebookApi
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

    protected function initFacebook(string $facebookAppId, string $facebookAppSecret, string $facebookDefaultToken): FacebookApi
    {
        $this->facebook = new Facebook(
            [
                'app_id' => $facebookAppId,
                'app_secret' => $facebookAppSecret,
                'default_graph_version' => 'v2.11',
                'default_access_token' => $facebookDefaultToken,
            ]
        );

        return $this;
    }

    protected function getRideEventId(Ride $ride): ?string
    {
        $facebook = $ride->getFacebook();

        if ($facebook) {
            $facebook = rtrim($facebook, '/');

            $parts = explode('/', $facebook);

            $eventId = array_pop($parts);

            return $eventId;
        }

        return null;
    }

    protected function getCityPageId(City $city): ?string
    {
        $facebook = $city->getFacebook();

        if ($facebook) {
            $facebook = rtrim($facebook, '/');

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }
}
