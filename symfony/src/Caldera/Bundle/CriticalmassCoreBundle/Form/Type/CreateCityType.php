<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $builder->add('longitude', 'Symfony\Component\Form\Extension\Core\Type\TextType');
                $builder->add('latitude', 'Symfony\Component\Form\Extension\Core\Type\TextType');

                break;
            case 2:

                break;
        }
    }

    public function getBlockPrefix() {
        return 'createCity';
    }
}