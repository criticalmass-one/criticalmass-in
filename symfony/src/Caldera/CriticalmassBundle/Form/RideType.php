<?php

namespace Caldera\CriticalmassBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date')
            ->add('hasTime')
            ->add('time')
            ->add('hasLocation')
            ->add('location')
            ->add('city')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Caldera\CriticalmassBundle\Entity\Ride'
        ));
    }

    public function getName()
    {
        return 'caldera_criticalmassbundle_ridetype';
    }
}
