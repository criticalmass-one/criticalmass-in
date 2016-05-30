<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateCityFlow extends FormFlow {

    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'wheels',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'engine',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
                'skip' => function($estimatedCurrentStepNumber, FormFlowInterface $flow) {
                    return $estimatedCurrentStepNumber > 1 && !$flow->getFormData()->canHaveEngine();
                },
            ),
            array(
                'label' => 'confirmation',
            ),
        );
    }

}