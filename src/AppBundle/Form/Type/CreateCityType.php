<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('city')
                    ->add('latitude', HiddenType::class)
                    ->add('longitude', HiddenType::class);

                break;

            case 2:
                $builder
                    ->add('title')
                    ->add('description')
                    ->add('punchLine')
                    ->add('longDescription')
                    ->add('cityPopulation');

                break;

            case 3:
                $builder
                    ->add('url')
                    ->add('facebook')
                    ->add('twitter');

                break;

            case 4:
                $builder
                    ->add('latitude', HiddenType::class)
                    ->add('longitude', HiddenType::class)
                    ->add('isStandardable', CheckboxType::class, ['required' => false])
                    ->add(
                        'standardDayOfWeek',
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
                        'standardWeekOfMonth',
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
                    ->add('isStandardableTime', CheckboxType::class, ['required' => false])
                    ->add('standardTime', TimeType::class, ['required' => false])
                    ->add('isStandardableLocation', CheckboxType::class, ['required' => false])
                    ->add('standardLocation', TextType::class, ['required' => false])
                    ->add('standardLatitude', HiddenType::class, ['required' => false])
                    ->add('standardLongitude', HiddenType::class, ['required' => false]);

                break;

            case 5:
                $builder
                    ->add('enableBoard')
                    ->add('timezone', TimezoneType::class);

                break;
            /*
                        case 6:
                            $builder
                                ->add('imageFile', 'vich_file', array('required' => false))
                            ;
            
                            break;
            */
        }
    }

    public function getBlockPrefix()
    {
        return 'city';
    }
}