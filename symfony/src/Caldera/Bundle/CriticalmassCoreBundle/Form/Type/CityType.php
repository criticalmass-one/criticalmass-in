<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', TextType::class, array('required' => false))
            ->add('title', TextType::class, array('required' => false))
            ->add('description', TextareaType::class, array('required' => false))
            ->add('url', TextType::class, array('required' => false))
            ->add('facebook', TextType::class, array('required' => false))
            ->add('twitter', TextType::class, array('required' => false))
            ->add('longitude', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('cityPopulation', IntegerType::class, array('required' => false))
            ->add('punchLine', TextType::class, array('required' => false))
            ->add('longDescription', TextareaType::class, array('required' => false));
    }

    public function getName()
    {
        return 'city';
    }
}