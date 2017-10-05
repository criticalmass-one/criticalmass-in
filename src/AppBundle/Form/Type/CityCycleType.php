<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CityCycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'longitude',
                HiddenType::class
            )
            ->add(
                'latitude',
                HiddenType::class
            )
            ->add(
                'dayOfWeek',
                ChoiceType::class,
                [
                    'label' => 'Wochentag',
                    'choices' => [
                        'Montag' => 1,
                        'Dienstag' => 2,
                        'Mittwoch' => 3,
                        'Donnerstag' => 4,
                        'Freitag' => 5,
                        'Sonnabend' => 6,
                        'Sonntag' => 0
                    ],
                    'required' => true
                ]
            )
            ->add(
                'weekOfMonth',
                ChoiceType::class,
                [
                    'label' => 'Woche im Monat',
                    'choices' => [
                        'Erste Woche im Monat' => 1,
                        'Zweite Woche im Monat' => 2,
                        'Dritte Woche im Monat' => 3,
                        'Vierte Woche im Monat' => 4,
                        'Letzte Woche im Monat' => 0
                    ],
                    'required' => true
                ]
            )
            ->add(
                'time',
                TimeType::class,
                [
                    'required' => false
                ]
            )
            ->add(
                'location',
                TextType::class,
                [
                    'required' => false
                ]
            )
        ;
    }

    public function getName(): string
    {
        return 'city_cycle';
    }
}