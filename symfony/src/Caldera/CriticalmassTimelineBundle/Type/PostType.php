<?php

namespace Caldera\CriticalmassTimelineBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'textarea', array('required' => false))
            ->add('latitude', 'hidden')
            ->add('longitude', 'hidden');
    }

    public function getName()
    {
        return 'post';
    }
}