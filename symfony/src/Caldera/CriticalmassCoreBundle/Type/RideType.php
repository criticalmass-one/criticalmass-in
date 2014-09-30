<?php

namespace Caldera\CriticalmassCoreBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('date', 'date')
            ->add('time', 'time')
            ->add('location', 'text')
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
            ->add('facebook', 'text')
            ->add('twitter', 'text')
            ->add('url', 'text')
            ->add('hasLocation', 'checkbox')
            ->add('hasTime', 'checkbox')
            ->add('weatherForecast', 'text')
            ->add('save', 'submit');
    }

    public function getName()
    {
        return 'ride';
    }
}