<?php

namespace AppBundle\Manager;

use Doctrine\Bundle\DoctrineBundle\Registry;
use FOS\ElasticaBundle\Elastica\Index;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;

abstract class AbstractElasticManager extends AbstractManager
{
    /** @var RepositoryManagerInterface $elasticManager */
    protected $elasticManager;

    public function __construct(Registry $doctrine, RepositoryManagerInterface $elasticManager)
    {
        parent::__construct($doctrine);

        $this->elasticManager = $elasticManager;
    }
}