<?php

namespace Criticalmass\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RideEstimateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('estimatedParticipants', TextType::class, array('required' => true));
    }

    public function getName()
    {
        return 'rideestimate';
    }
}
