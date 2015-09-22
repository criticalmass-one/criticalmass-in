<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

class CyclewaysController extends AbstractController
{
    public function mapAction()
    {
        $incidents = $this->getIncidentRepository()->findAll();
        
        return $this->render(
            'CalderaCyclewaysBundle:Cycleways:map.html.twig', 
            [
                'incidents' => $incidents
            ]);
    }
}
