<?php declare(strict_types=1);

namespace App\Criticalmass\Facebook\Api;

use Facebook\GraphNodes\GraphPage;

class FacebookPageApi extends FacebookApi
{
    protected $standardFields = [
        'name',
        'about',
        'description',
        'were_here_count',
        'general_info',
        'website',
        'fan_count',
    ];

    public function queryPage($pageId, array $fields = []): ?GraphPage
    {
        $fieldString = implode(',', $this->getQueryFields($fields));

        try {
            $endpoint = sprintf('/%s?fields=%s', $pageId, $fieldString);

            $response = $this->facebook->get($endpoint);
        } catch (\Exception $e) {
            return null;
        }

        /** @var GraphPage $page */
        $page = null;

        try {
            $page = $response->getGraphPage();
        } catch (\Exception $e) {
            return null;
        }

        return $page;
    }
}
