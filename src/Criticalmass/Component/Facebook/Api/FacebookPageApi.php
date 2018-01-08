<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook\Api;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\FacebookCityProperties;
use Facebook\GraphNodes\GraphPage;

class FacebookPageApi extends FacebookApi
{
    public function getPagePropertiesForCity(City $city)
    {
        $pageId = $this->getCityPageId($city);

        if (!$pageId) {
            return null;
        }

        $fields = [
            'name',
            'about',
            'description',
            'were_here_count',
            'general_info',
            'website',
            'fan_count',
        ];

        /** @var GraphPage $page */
        $page = $this->queryPage($pageId, $fields);

        if ($page) {
            $properties = new FacebookCityProperties();

            $properties
                ->setCity($city)
                ->setName($page->getName())
                ->setAbout($page->getField('about'))
                ->setDescription($page->getField('description'))
                ->setGeneralInfo($page->getField('general_info'))
                ->setCheckinNumber($page->getField('were_here_count'))
                ->setWebsite($page->getField('website'))
                ->setLikeNumber($page->getField('fan_count'))
            ;

            return $properties;
        }

        return null;
    }

    protected function queryPage($pageId, array $fields = [])
    {
        $fieldString = implode(',', $fields);

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
