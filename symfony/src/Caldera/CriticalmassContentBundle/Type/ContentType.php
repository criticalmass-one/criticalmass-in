<?php

namespace Caldera\CriticalmassContentBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array('required' => true))
            ->add('text', 'textarea', array('required' => true))
            ->add('slug', 'text', array('required' => false));
    }

    public function getName()
    {
        return 'content';
    }
}