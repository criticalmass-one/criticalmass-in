<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CreateCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        switch ($options['flow_step']) {
            case 1:
                $builder
                    ->add('city')
                    ->add('longitude', 'hidden')
                    ->add('longitude', 'hidden')
                ;

                break;

            case 2:
                $builder
                    ->add('title')
                    ->add('description')
                    ->add('punchLine')
                    ->add('longDescription')
                    ->add('cityPopulation')
                ;

                break;

            case 3:
                $builder
                    ->add('url')
                    ->add('facebook')
                    ->add('twitter')
                ;

            case 4:
                $builder
                    ->add('isStandardable', 'checkbox', array('required' => false))
                    ->add('standardDayOfWeek', 'choice', array('label' => 'Wochentag', 'choices' => array(1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Sonnabend', 0 => 'Sonntag'), 'required' => true))
                    ->add('standardWeekOfMonth', 'choice', array('label' => 'Woche im Monat', 'choices' => array(1 => 'Erste Woche im Monat', 2 => 'Zweite Woche im Monat', 3 => 'Dritte Woche im Monat', 4 => 'Vierte Woche im Monat', 0 => 'Letzte Woche im Monat'), 'required' => true))
                    ->add('isStandardableTime', 'checkbox', array('required' => false))
                    ->add('standardTime', 'time', array('required' => false))
                    ->add('isStandardableLocation', 'checkbox', array('required' => false))
                    ->add('standardLocation', 'text', array('required' => false))
                    ->add('standardLatitude', 'hidden', array('required' => false))
                    ->add('standardLongitude', 'hidden', array('required' => false))
                ;
        }
    }

    public function getBlockPrefix() {
        return 'city';
    }
}