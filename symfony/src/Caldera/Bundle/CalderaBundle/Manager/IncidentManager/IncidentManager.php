<?php

namespace Caldera\Bundle\CalderaBundle\Manager\IncidentManager;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CalderaBundle\Manager\AbstractElasticManager;
use Caldera\Bundle\CalderaBundle\Manager\AbstractManager;
use Caldera\Bundle\CalderaBundle\Manager\Util\Bounds;
use Caldera\Bundle\CalderaBundle\Repository\IncidentRepository;
use Caldera\Bundle\CalderaBundle\Repository\PostRepository;

class IncidentManager extends AbstractElasticManager
{
    /** @var IncidentRepository $incidentRepository */
    protected $incidentRepository = null;

    public function __construct($doctrine, $elasticIndex)
    {
        parent::__construct($doctrine, $elasticIndex);

        $this->incidentRepository = $this->doctrine->getRepository('CalderaBundle:Incident');
    }

    public function getIncidentsInBounds(Bounds $bounds): array
    {
        $geoFilter = new \Elastica\Filter\GeoBoundingBox('pin', $bounds->toLatLonArray());

        $filteredQuery = new \Elastica\Query\Filtered(new \Elastica\Query\MatchAll(), $geoFilter);

        $query = new \Elastica\Query($filteredQuery);

        $query->setSize(500);

        $result = $this->elasticManager->getRepository('CalderaBundle:Incident')->find($query);

        return $result;
    }

    public function getIncidentsForCity(City $city): array
    {
        return $this->incidentRepository->findAll();
    }
}