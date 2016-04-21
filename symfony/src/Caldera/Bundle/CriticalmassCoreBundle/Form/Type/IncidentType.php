<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class IncidentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('polyline', 'hidden')
            ->add('visibleFrom', 'date')
            ->add('visibleTo', 'date')
            ->add('expires', 'checkbox');
    }

    public function getName()
    {
        return 'incident';
    }
}