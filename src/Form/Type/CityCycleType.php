<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\CityCycle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class CityCycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var CityCycle $cycle */
        $cycle = $builder->getData();

        /** @var string $timezone */
        $timezone = $cycle->getCity()->getTimezone();

        $builder
            ->add('longitude', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('time', TimeType::class, [
                'required' => false,
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
            ])
            ->add('location', TextType::class, [
                'required' => false,
            ])
            ->add('validFrom', DateType::class, [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'required' => false,
                'html5' => false, // @todo remvoe this later
            ])
            ->add('validUntil', DateType::class, [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'required' => false,
                'html5' => false, // @todo remvoe this later
            ]);

        if (!$cycle->getRideCalculatorFqcn()) {
            $builder
                ->add('dayOfWeek', ChoiceType::class, [
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
                ])
                ->add('weekOfMonth', ChoiceType::class, [
                    'label' => 'Woche im Monat',
                    'choices' => [
                        'Erste Woche im Monat' => 1,
                        'Zweite Woche im Monat' => 2,
                        'Dritte Woche im Monat' => 3,
                        'Vierte Woche im Monat' => 4,
                        'Letzte Woche im Monat' => 0
                    ],
                    'required' => true
                ]);
        } else {
            $builder
                ->add('dayOfWeek', TextType::class, [
                    'disabled' => true,
                    'data' => $cycle->getSpecialDayOfWeek(),
                ])
                ->add('weekOfMonth', TextType::class, [
                    'disabled' => true,
                    'data' => $cycle->getSpecialWeekOfMonth(),
                ]);
        }
    }

    public function getName(): string
    {
        return 'city_cycle';
    }
}
