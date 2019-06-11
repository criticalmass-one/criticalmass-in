<?php declare(strict_types=1);

namespace App\Criticalmass\ElasticCityFinder;

use App\Entity\City;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\FinderInterface;

class ElasticCityFinder implements ElasticCityFinderInterface
{
    /** @var FinderInterface $finder */
    protected $finder;

    public function __construct(FinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function findNearCities(City $city, int $size = 15, int $distance = 50): array
    {
        if (!$city->getLatitude() || !$city->getLongitude()) {
            return [];
        }

        $query = $this->createQuery($city, $size, $distance);

        try {
            return $this->finder->find($query);
        } catch (\Exception $exception) {
            return [];
        }
    }

    protected function createQuery(City $city, int $size = 15, int $distance = 50): Query
    {
        $kmDistance = sprintf('%dkm', $distance);

        $enabledQuery = new \Elastica\Query\Term(['isEnabled' => true]);

        $selfTerm = new \Elastica\Query\Term(['id' => $city->getId()]);
        $selfQuery = new \Elastica\Query\BoolQuery();
        $selfQuery->addMustNot($selfTerm);

        $geoQuery = new \Elastica\Query\GeoDistance('pin', [
            'lat' => $city->getLatitude(),
            'lon' => $city->getLongitude(),
        ],
            $kmDistance
        );

        $boolQuery = new \Elastica\Query\BoolQuery();
        $boolQuery
            ->addMust($geoQuery)
            ->addMust($enabledQuery)
            ->addMust($selfQuery);

        $query = new \Elastica\Query($boolQuery);

        $query->setSize($size);
        $query->setSort([
            '_geo_distance' => [
                'pin' => [
                    $city->getLongitude(),
                    $city->getLatitude(),
                ],
                'order' => 'desc',
                'unit' => 'km',
            ]
        ]);

        return $query;
    }
}