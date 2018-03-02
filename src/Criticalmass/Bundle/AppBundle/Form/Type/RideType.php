<?php

namespace Criticalmass\Bundle\AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RideType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => false])
            ->add('description', TextareaType::class, ['required' => false])
            ->add('socialDescription', TextareaType::class, ['required' => false])
            ->add('dateTime', DateTimeType::class, [
                'model_timezone' => 'UTC',
                'view_timezone' => 'Europe/Berlin',
                'date_widget' => 'single_text',
                'date_format' => 'dd.MM.yyyy', //here
                'time_widget' => 'single_text',
                'compound' => true
            ])
            ->add('location', TextType::class, ['required' => false])
            ->add('latitude', HiddenType::class, ['required' => false])
            ->add('longitude', HiddenType::class, ['required' => false])
            ->add('facebook', TextType::class, ['required' => false])
            ->add('twitter', TextType::class, ['required' => false])
            ->add('url', TextType::class, ['required' => false])
            ->add('hasLocation', CheckboxType::class, ['required' => false])
            ->add('hasTime', CheckboxType::class, ['required' => false])
            ->add('save', SubmitType::class)
            ->add('imageFile',
                VichFileType::class,
                [
                    'required' => false
                ]);
    }

    public function getName(): string
    {
        return 'ride';
    }
}
