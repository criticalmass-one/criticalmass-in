<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AppType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('required' => false))
            ->add('description', TextareaType::class, array('required' => false))
            ->add('url', TextType::class, array('required' => false))
            ->add('enabled', ChoiceType::class, array('label' => 'Aktivierung', 'choices' => array(0 => 'inaktiv', 1 => 'aktiv'), 'required' => true));
    }

    public function getName()
    {
        return 'app';
    }
}