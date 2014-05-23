<?php

namespace Caldera\CriticalmassAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RideAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('city', null, array('label' => 'Stadt'))
            ->add('date', 'date', array('label' => 'Datum'))
            ->add('hasTime', 'checkbox', array('label' => 'Uhrzeit anzeigen?'))
            ->add('time', 'time', array('label' => 'Uhrzeit'))
            ->add('hasLocation', 'checkbox', array('label' => 'Treffpunkt anzeigen?'))
            ->add('location', 'text', array('label' => 'Treffpunkt'))
            ->add('latitude', 'text', array('label' => 'Breitengrad'))
            ->add('longitude', 'text', array('label' => 'Längengrad'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('city')
            ->add('location')
            ->add('date')
            ->add('time')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('date')
            ->addIdentifier('city')
            ->addIdentifier('hasLocation')
            ->addIdentifier('location')
            ->addIdentifier('hasTime')
            ->addIdentifier('time')
        ;
    }
}