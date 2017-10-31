<?php

namespace AppBundle\Facebook;

use AppBundle\Entity\City;
use AppBundle\Entity\FacebookCityProperties;
use Facebook\GraphNodes\GraphPage;

class FacebookPageApi extends AbstractFacebookApi
{
    protected $standardFields = [
        'name',
        'about',
        'description',
        'likes',
        'were_here_count',
        'general_info',
        'website',
    ];

    public function getPagePropertiesForCity(City $city): ?FacebookCityProperties
    {
        $pageId = $this->getCityPageId($city);

        /** @var GraphPage $page */
        $page = $this->queryPage($pageId, $this->standardFields);

        if ($page) {
            return $this->createCityProperties($city, $page);
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

    protected function createCityProperties(City $city, GraphPage $page): FacebookCityProperties
    {
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
}