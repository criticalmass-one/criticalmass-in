<?php

namespace Caldera\Bundle\CalderaBundle\Manager;

abstract class AbstractElasticManager extends AbstractManager
{
    protected $elasticIndex;

    public function __construct($doctrine, $elasticIndex)
    {
        parent::__construct($doctrine);

        $this->elasticIndex = $elasticIndex;
    }
}