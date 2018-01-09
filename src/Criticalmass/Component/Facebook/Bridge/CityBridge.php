<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook\Bridge;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\FacebookCityProperties;
use Criticalmass\Component\Facebook\Api\FacebookPageApi;

class CityBridge extends AbstractBridge
{
    /** @var FacebookPageApi $facebookPageApi */
    protected $facebookPageApi;

    public function __construct(FacebookPageApi $facebookPageApi)
    {
        $this->facebookPageApi = $facebookPageApi;
    }

    public function getPagePropertiesForCity(City $city): ?FacebookCityProperties
    {
        $pageId = $this->getCityPageId($city);

        if (!$pageId) {
            return null;
        }

        $page = $this->facebookPageApi->queryPage($pageId);

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
}
