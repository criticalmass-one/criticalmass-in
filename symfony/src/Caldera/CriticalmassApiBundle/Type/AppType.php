<?php

namespace Caldera\CriticalmassApiBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AppType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('url', 'text', array('required' => false))
            ->add('enabled', 'choice', array('label' => 'Aktivierung', 'choices' => array(0 => 'inaktiv', 1 => 'aktiv'), 'required' => true));
    }

    public function getName()
    {
        return 'app';
    }
}