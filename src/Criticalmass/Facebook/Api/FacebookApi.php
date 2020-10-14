<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook\Api;

use Facebook\Facebook;
use Facebook\FacebookResponse;

class FacebookApi
{
    /** @var Facebook $facebook */
    protected $facebook;

    /** @var array $standardFields */
    protected $standardFields = [];

    public function __construct(string $facebookAppId, string $facebookAppSecret, string $facebookDefaultToken)
    {
        $this->initFacebook(
            $facebookAppId,
            $facebookAppSecret,
            $facebookDefaultToken
        );
    }

    protected function initFacebook(
        string $facebookAppId,
        string $facebookAppSecret,
        string $facebookDefaultToken
    ): FacebookApi {
        $this->facebook = new Facebook([
            'app_id' => $facebookAppId,
            'app_secret' => $facebookAppSecret,
            'default_graph_version' => 'v2.11',
            'default_access_token' => $facebookDefaultToken,
        ]);

        return $this;
    }

    public function query(string $endpoint): FacebookResponse
    {
        return $this->facebook->get($endpoint);
    }

    protected function getQueryFields(array $fields): array
    {
        return array_merge($this->standardFields, $fields);
    }
}
