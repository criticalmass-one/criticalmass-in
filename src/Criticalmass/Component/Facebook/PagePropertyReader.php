<?php declare(strict_types=1);

namespace Criticalmass\Component\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Component\Facebook\Api\FacebookPageApi;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class PagePropertyReader
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var FacebookPageApi $facebookPageApi */
    protected $facebookPageApi;

    /** @var array $readCities */
    protected $propertyList = [];

    public function __construct(Doctrine $doctrine, FacebookPageApi $facebookPageApi)
    {
        $this->doctrine = $doctrine;
        $this->facebookPageApi = $facebookPageApi;
    }

    public function read(): PagePropertyReader
    {
        $cities = $this->doctrine->getRepository(City::class)->findCitiesWithFacebook();

        /** @var City $city */
        foreach ($cities as $city) {
            $properties = $this->facebookPageApi->getPagePropertiesForCity($city);

            if ($properties) {
                $this->doctrine->getManager()->persist($properties);

                $this->propertyList[] = $properties;
            }
        }

        $this->doctrine->getManager()->flush();

        return $this;
    }

    public function getPropertyList(): array
    {
        return $this->propertyList;
    }
}
