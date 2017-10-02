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

class RideAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('city')
            ->add('title')
            ->add('description')
            ->add('socialDescription')
            ->add('dateTime')
            ->add('hasTime')
            ->add('hasLocation')
            ->add('location')
            ->add('latitude')
            ->add('longitude')
            ->add('estimatedParticipants')
            ->add('estimatedDuration')
            ->add('facebook')
            ->add('twitter')
            ->add('url')
            ->add('restrictedPhotoAccess')
            ->add('imageFile', VichImageType::class)
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('dateTime')
            ->add('title')
            ->add('description')
            ->add('location')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->add('title')
            ->add('city')
            ->add('location')
        ;
    }
}
