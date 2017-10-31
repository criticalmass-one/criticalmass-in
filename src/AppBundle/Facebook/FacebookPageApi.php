<?php

namespace AppBundle\Facebook;

use AppBundle\Entity\City;
use AppBundle\Entity\FacebookCityProperties;
use Facebook\GraphNodes\GraphPage;

class FacebookPageApi extends AbstractFacebookApi
{
    public function getPagePropertiesForCity(City $city): ?FacebookCityProperties
    {
        $pageId = $this->getCityPageId($city);

        $fields = [
            'name',
            'about',
            'description',
            'likes',
            'were_here_count',
            'general_info',
            'website',
        ];

        /** @var GraphPage $page */
        $page = $this->queryPage($pageId, $fields);

        if ($page) {
            $properties = new FacebookCityProperties();

            $properties->setCity($city);

            $properties
                ->setName($page->getName())
                ->setAbout($page->getField('about'))
                ->setDescription($page->getField('description'))
                ->setGeneralInfo($page->getField('general_info'))
                ->setCheckinNumber($page->getField('were_here_count'))
                ->setLikeNumber($page->getField('likes'))
                ->setWebsite($page->getField('website'));

            return $properties;
        }

        return null;
    }

    protected function queryPage($pageId, array $fields = []): ?GraphPage
    {
        $queryString = sprintf('/%s?fields=%s', $pageId,  implode(',', $fields));

        try {
            $response = $this->facebook->get($queryString);

            $page = $response->getGraphPage();
        } catch (\Exception $e) {
            return null;
        }

        return $page;
    }
}