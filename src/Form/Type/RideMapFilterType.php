<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Enum\RideTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Die Filter der Übersichtskarte.
 *
 * Das Formular schreibt nichts, es beschreibt nur eine Ansicht. Deshalb GET,
 * kein CSRF-Token und kein Namensraum: Die Adresse soll `?date=…&q=…` heissen
 * und sich weitergeben lassen.
 */
class RideMapFilterType extends AbstractType
{
    /** Tag, Woche (Montag bis Sonntag) oder Kalendermonat um das gewaehlte Datum. */
    public const ZEITRAEUME = ['day', 'week', 'month'];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'label' => 'Datum',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => true,
            ])
            ->add('range', ChoiceType::class, [
                'label' => 'Zeitraum',
                'choices' => ['Tag' => 'day', 'Woche' => 'week', 'Monat' => 'month'],
                'required' => true,
            ])
            ->add('q', SearchType::class, [
                'label' => 'Suche',
                'required' => false,
            ])
            ->add('active', CheckboxType::class, [
                'label' => 'Nur aktive Städte',
                'required' => false,
            ])
            ->add('type', EnumType::class, [
                'label' => 'Art der Tour',
                'class' => RideTypeEnum::class,
                'choice_label' => static fn (RideTypeEnum $typ): string => $typ->label(),
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
