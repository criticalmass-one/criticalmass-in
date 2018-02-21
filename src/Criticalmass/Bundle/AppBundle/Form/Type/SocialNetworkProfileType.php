<?php

namespace Criticalmass\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SocialNetworkProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', TextType::class, ['required' => false])
            ->add('network', ChoiceType::class, ['required' => false])
            ->add('mainNetwork', CheckboxType::class, ['required' => false])
        ;
    }

    public function getName()
    {
        return 'social_network_profile';
    }
}
