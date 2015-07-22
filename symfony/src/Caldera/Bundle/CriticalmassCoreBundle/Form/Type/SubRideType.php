<?php

namespace Caldera\CriticalmassCoreBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SubRideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('time', 'time')
            ->add('location', 'text', array('required' => false))
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden')
            ->add('facebook', 'text', array('required' => false))
            ->add('twitter', 'text', array('required' => false))
            ->add('url', 'text', array('required' => false));
    }

    public function getName()
    {
        return 'subride';
    }
}