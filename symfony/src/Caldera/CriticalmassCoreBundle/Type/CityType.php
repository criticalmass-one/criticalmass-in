<?php

namespace Caldera\CriticalmassCoreBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', 'text')
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('url', 'text')
            ->add('facebook', 'text')
            ->add('twitter', 'text')
            ->add('longitude', 'hidden')
            ->add('latitude', 'hidden')
            ->add('cityPopulation', 'text')
            ->add('punchLine', 'text')
            ->add('longDescription', 'textarea');
    }

    public function getName()
    {
        return 'city';
    }
}