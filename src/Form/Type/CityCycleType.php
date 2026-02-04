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
    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    'label' => 'form.city_cycle.day_of_week',
                    'choices' => [
                        'form.city_cycle.monday' => 1,
                        'form.city_cycle.tuesday' => 2,
                        'form.city_cycle.wednesday' => 3,
                        'form.city_cycle.thursday' => 4,
                        'form.city_cycle.friday' => 5,
                        'form.city_cycle.saturday' => 6,
                        'form.city_cycle.sunday' => 0,
                    ],
                    'choice_translation_domain' => 'messages',
                    'required' => true,
                ])
                ->add('weekOfMonth', ChoiceType::class, [
                    'label' => 'form.city_cycle.week_of_month',
                    'choices' => [
                        'form.city_cycle.first_week' => 1,
                        'form.city_cycle.second_week' => 2,
                        'form.city_cycle.third_week' => 3,
                        'form.city_cycle.fourth_week' => 4,
                        'form.city_cycle.last_week' => 0,
                    ],
                    'choice_translation_domain' => 'messages',
                    'required' => true,
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
