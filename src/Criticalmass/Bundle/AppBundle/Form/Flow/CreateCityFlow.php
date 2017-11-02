<?php

namespace AppBundle\Form\Flow;

use Craue\FormFlowBundle\Form\FormFlow;

class CreateCityFlow extends FormFlow
{

    protected function loadStepsConfig()
    {
        return [
            [
                'label' => 'Stadt',
                'form_type' => 'AppBundle\Form\Type\CreateCityType',
            ],
            [
                'label' => 'Beschreibung',
                'form_type' => 'AppBundle\Form\Type\CreateCityType',
            ],
            [
                'label' => 'Soziale Netze',
                'form_type' => 'AppBundle\Form\Type\CreateCityType',
            ],
            [
                'label' => 'Touren-Generator',
                'form_type' => 'AppBundle\Form\Type\CreateCityType',
            ],
            [
                'label' => 'Technisches',
                'form_type' => 'AppBundle\Form\Type\CreateCityType',
            ],
            /*            array(
                            'label' => 'Header-Grafik',
                            'form_type' => 'AppBundle\Form\Type\CreateCityType',
                        ),
            */
            [
                'label' => 'confirmation',
            ],
        ];
    }

}