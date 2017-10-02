<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CityAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('region')
            ->add('mainSlug')
            ->add('city')
            ->add('title')
            ->add('description')
            ->add('url')
            ->add('facebook')
            ->add('twitter')
            ->add('latitude')
            ->add('longitude')
            ->add('isStandardable')
            ->add('standardDayOfWeek')
            ->add('standardWeekOfMonth')
            ->add('isStandardableTime')
            ->add('standardTime')
            ->add('isStandardableLocation')
            ->add('standardLocation')
            ->add('standardLatitude')
            ->add('standardLongitude')
            ->add('cityPopulation')
            ->add('punchline', TextType::class)
            ->add('longdescription', TextType::class)
            ->add('imageFile', VichImageType::class)
            ->add('enableBoard')
            ->add('timezone')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
        ;
    }
}
