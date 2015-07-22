<?php

namespace Caldera\CriticalmassStatisticBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RideEstimateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estimatedParticipants', 'text', array('required' => false))
            ->add('estimatedDistance', 'text', array('required' => false))
            ->add('estimatedDuration', 'text', array('required' => false));
    }

    public function getName()
    {
        return 'rideestimate';
    }
}