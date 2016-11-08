<?php

namespace Caldera\Bundle\CalderaBundle\Manager\IncidentManager;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Caldera\Bundle\CalderaBundle\Manager\AbstractElasticManager;
use Caldera\Bundle\CalderaBundle\Manager\AbstractManager;
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

    public function getIncidentsInBounds(Incident $incident): array
    {
        return $this->postRepository->getPostsForIncident($incident);
    }
}