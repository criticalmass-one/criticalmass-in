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
            ->with('Details', ['class' => 'col-md-6'])
            ->add('city')
            ->add('title')
            ->add('description')
            ->add('socialDescription')
            ->add('restrictedPhotoAccess')
            ->end()
            ->with('Vorschaubild', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class)
            ->end()
            ->with('Soziale Netze', ['class' => 'col-md-6'])
            ->add('facebook')
            ->add('twitter')
            ->add('url')
            ->end()
            ->with('Uhrzeit', ['class' => 'col-md-6'])
            ->add('hasTime')
            ->add('dateTime')
            ->end()
            ->with('Treffpunkt', ['class' => 'col-md-6'])
            ->add('hasLocation')
            ->add('location')
            ->add('latitude')
            ->add('longitude')
            ->end()
            ->with('Statistik', ['class' => 'col-md-6'])
            ->add('estimatedParticipants')
            ->add('estimatedDuration')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('dateTime')
            ->add('title')
            ->add('description')
            ->add('location');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->add('title')
            ->add('city')
            ->add('location');
    }
}
