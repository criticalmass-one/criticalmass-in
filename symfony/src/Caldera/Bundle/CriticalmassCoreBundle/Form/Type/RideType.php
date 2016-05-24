<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('date', 'date',
                [
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'Europe/Berlin'
                ])
            ->add('time', 'time',
                [
                    'model_timezone' => 'UTC',
                    'view_timezone' => 'Europe/Berlin'
                ])
            ->add('location', 'text', array('required' => false))
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
            ->add('facebook', 'text', array('required' => false))
            ->add('twitter', 'text', array('required' => false))
            ->add('url', 'text', array('required' => false))
            ->add('hasLocation', 'checkbox')
            ->add('hasTime', 'checkbox')
            ->add('save', 'submit');
    }

    public function getName()
    {
        return 'ride';
    }
}