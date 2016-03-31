<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Facebook;

use Caldera\Bundle\CriticalmassCoreBundle\Utils\DateTimeUtils;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\FacebookCityProperties;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Facebook\GraphNodes\GraphEdge;
use Facebook\GraphNodes\GraphEvent;
use Facebook\GraphNodes\GraphPage;

class FacebookPageApi extends FacebookApi
{
    public function getPagePropertiesForCity(City $city)
    {
        $pageId = $this->getCityPageId($city);

        $fields = [
            'name',
            'about',
            'description',
            'likes',
            'were_here_count',
            'general_info',
            'website'
        ];

        /**
         * @var GraphPage $page
         */
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
                ->setWebsite($page->getField('website'))
            ;

            return $properties;
        }

        return null;
    }

    protected function queryPage($pageId, array $fields = [])
    {
        $fieldString = implode(',', $fields);

        try {
            $response = $this->facebook->get('/' . $pageId.'?fields='.$fieldString);
        } catch (\Exception $e) {
            return null;
        }

        /**
         * @var GraphPage $page
         */
        $page = null;

        try {
            $page = $response->getGraphPage();
        } catch (\Exception $e) {
            return null;
        }

        return $page;
    }
}