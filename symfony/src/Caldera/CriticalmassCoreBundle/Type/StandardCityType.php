<?php

namespace Caldera\CriticalmassCoreBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StandardCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', 'text', array('required' => false))
            ->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('url', 'text', array('required' => false))
            ->add('facebook', 'text', array('required' => false))
            ->add('twitter', 'text', array('required' => false))
            ->add('longitude', 'hidden')
            ->add('latitude', 'hidden')
            ->add('cityPopulation', 'integer', array('required' => false))
            ->add('punchLine', 'text', array('required' => false))
            ->add('longDescription', 'textarea', array('required' => false))
            ->add('isStandardable', 'textarea', array('required' => false))
            ->add('standardDayOfWeek', 'textarea', array('required' => false))
            ->add('standardWeekOfMonth', 'textarea', array('required' => false))
            ->add('standardTime', 'time', array('required' => false))
            ->add('standardLocation', 'textarea', array('required' => false))
            ->add('standardLatitude', 'textarea', array('required' => false))
            ->add('standardLongitude', 'textarea', array('required' => false));
    }

    public function getName()
    {
        return 'city';
    }
}