<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class StandardCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city', 'text', array('required' => false))
            ->add('title', 'text', array('required' => false))
            ->add('description', 'textarea', array('required' => false))
            ->add('url', 'text', array('required' => false))
            ->add('facebook', 'text', array('required' => false))
            ->add('twitter', 'text', array('required' => false))
            ->add('longitude', 'hidden')
            ->add('latitude', 'hidden')
            ->add('cityPopulation', 'integer', array('required' => false))
            ->add(
                'region',
                'entity',
                [
                    'class' => 'CalderaCriticalmassModelBundle:Region',
                    'query_builder' => function(\Caldera\Bundle\CriticalmassModelBundle\Repository\RegionRepository $er)
                    {
                        $builder = $er->createQueryBuilder('region');
                        
                        $builder->where($builder->expr()->isNotNull('region.parent'));

                        $builder->orderBy('region.name', 'ASC');

                        return $builder;
                    },
                    'group_by' => 'parent'
                ]
            )
            ->add('punchLine', 'text', array('required' => false))
            ->add('longDescription', 'textarea', array('required' => false))
            ->add('enableBoard')
            ->add('timezone', 'timezone')
            ->add('isStandardable', 'checkbox', array('required' => false))
            ->add('standardDayOfWeek', 'choice', array('label' => 'Wochentag', 'choices' => array(1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Sonnabend', 0 => 'Sonntag'), 'required' => true))
            ->add('standardWeekOfMonth', 'choice', array('label' => 'Woche im Monat', 'choices' => array(1 => 'Erste Woche im Monat', 2 => 'Zweite Woche im Monat', 3 => 'Dritte Woche im Monat', 4 => 'Vierte Woche im Monat', 0 => 'Letzte Woche im Monat'), 'required' => true))
            ->add('isStandardableTime', 'checkbox', array('required' => false))
            ->add('standardTime', 'time', array('required' => false))
            ->add('isStandardableLocation', 'checkbox', array('required' => false))
            ->add('standardLocation', 'text', array('required' => false))
            ->add('standardLatitude', 'hidden', array('required' => false))
            ->add('standardLongitude', 'hidden', array('required' => false))
            ->add('imageFile', 'vich_file', array('required' => false));
    }

    public function getName()
    {
        return 'city';
    }
}