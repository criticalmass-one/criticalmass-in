<?php

namespace AppBundle\Form\Type;

use AppBundle\Repository\RegionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class StandardCityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('city',
                TextType::class, [
                    'required' => false
                ]
            )
            ->add('title',
                TextType::class, [
                    'required' => false
                ]
            )
            ->add('description',
                TextareaType::class, [
                    'required' => false
                ]
            )
            ->add('longitude',
                HiddenType::class
            )
            ->add('latitude',
                HiddenType::class
            )
            ->add('cityPopulation',
                IntegerType::class, [
                    'required' => false
                ]
            )
            ->add('region',
                EntityType::class,
                [
                    'class' => 'AppBundle:Region',
                    'query_builder' => function (RegionRepository $er) {
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
            ->add('punchLine',
                TextType::class, [
                    'required' => false
                ]
            )
            ->add('longDescription',
                TextareaType::class, [
                    'required' => false
                ]
            )
            ->add('enableBoard',
                CheckboxType::class, [
                    'required' => false
                ]
            )
            ->add('timezone',
                TimezoneType::class
            )
            ->add('imageFile',
                VichFileType::class, [
                    'required' => false
                ]
            );
    }

    public function getName()
    {
        return 'city';
    }
}
