<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class StandardCityType extends AbstractType
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
            ->add('cityPopulation', IncidentType::class, array('required' => false))
            ->add(
                'region',
                EntityType::class,
                [
                    'class' => 'CalderaBundle:Region',
                    'query_builder' => function (\Caldera\Bundle\CalderaBundle\Repository\RegionRepository $er) {
                        $builder = $er->createQueryBuilder('region');

                        $builder->join('region.parent', 'region2');
                        $builder->join('region2.parent', 'region3');

                        $builder->where($builder->expr()->isNotNull('region3.parent'));

                        $builder->orderBy('region2.name', 'ASC');
                        $builder->addOrderBy('region.name', 'ASC');

                        return $builder;
                    },
                    'group_by' => 'parent'
                ]
            )
            ->add('punchLine', TextType::class, array('required' => false))
            ->add('longDescription', TextareaType::class, array('required' => false))
            ->add('enableBoard', CheckboxType::class)
            ->add('timezone', TimezoneType::class)
            ->add('isStandardable', CheckboxType::class, array('required' => false))
            ->add('standardDayOfWeek', ChoiceType::class, array('label' => 'Wochentag', 'choices' => array(1 => 'Montag', 2 => 'Dienstag', 3 => 'Mittwoch', 4 => 'Donnerstag', 5 => 'Freitag', 6 => 'Sonnabend', 0 => 'Sonntag'), 'required' => true))
            ->add('standardWeekOfMonth', ChoiceType::class, array('label' => 'Woche im Monat', 'choices' => array(1 => 'Erste Woche im Monat', 2 => 'Zweite Woche im Monat', 3 => 'Dritte Woche im Monat', 4 => 'Vierte Woche im Monat', 0 => 'Letzte Woche im Monat'), 'required' => true))
            ->add('isStandardableTime', CheckboxType::class, array('required' => false))
            ->add('standardTime', TimeType::class, array('required' => false))
            ->add('isStandardableLocation', CheckboxType::class, array('required' => false))
            ->add('standardLocation', TextType::class, array('required' => false))
            ->add('standardLatitude', HiddenType::class, array('required' => false))
            ->add('standardLongitude', HiddenType::class, array('required' => false))
            ->add('imageFile', VichFileType::class, array('required' => false));
    }

    public function getName()
    {
        return 'city';
    }
}