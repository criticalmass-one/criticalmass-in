<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class SubrideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('required' => false))
            ->add('description', TextareaType::class, array('required' => false))
            ->add('time', TimeType::class)
            ->add('location', TextType::class, array('required' => false))
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class);
    }

    public function getName()
    {
        return 'subride';
    }
}
