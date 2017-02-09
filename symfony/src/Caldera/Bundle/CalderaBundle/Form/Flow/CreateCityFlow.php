<?php

namespace Caldera\Bundle\CalderaBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;

class CreateCityFlow extends FormFlow
{

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'Stadt',
                'form_type' => 'Caldera\Bundle\CalderaBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Beschreibung',
                'form_type' => 'Caldera\Bundle\CalderaBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Soziale Netze',
                'form_type' => 'Caldera\Bundle\CalderaBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Touren-Generator',
                'form_type' => 'Caldera\Bundle\CalderaBundle\Form\Type\CreateCityType',
            ),
            array(
                'label' => 'Technisches',
                'form_type' => 'Caldera\Bundle\CalderaBundle\Form\Type\CreateCityType',
            ),
            /*            array(
                            'label' => 'Header-Grafik',
                            'form_type' => 'Caldera\Bundle\CalderaBundle\Form\Type\CreateCityType',
                        ),
            */
            array(
                'label' => 'confirmation',
            ),
        );
    }

}