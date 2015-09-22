<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Repository\IncidentRepository;
use \Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController as CriticalmassAbstractController;

abstract class AbstractController extends CriticalmassAbstractController
{
    /**
     * @return IncidentRepository
     */
    protected function getIncidentRepository()
    {
        return $this->getDoctrine()->getRepository('CalderaCyclewaysBundle:Incident');
    }
}
