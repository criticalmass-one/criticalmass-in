<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Form\FormFlowInterface;

class CreateCityFlow extends FormFlow {

    protected function loadStepsConfig() {
        return array(
            array(
                'label' => 'Stadt',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Beschreibung',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Soziale Netze',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Touren-Generator',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Technisches',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
/*            array(
                'label' => 'Header-Grafik',
                'form_type' => 'Caldera\Bundle\CriticalmassCoreBundle\Form\Type\CreateCityType',
            ),
*/            array(
                'label' => 'confirmation',
            ),
        );
    }

}