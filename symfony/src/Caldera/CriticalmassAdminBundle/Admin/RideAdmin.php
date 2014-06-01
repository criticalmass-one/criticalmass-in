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
            ->with('Stadt', array('class' => 'col-md-6'))
                ->add('city', null, array('label' => 'Stadt'))
            ->end()
            ->with('Zusatzinformationen', array('class' => 'col-md-6'))
                ->add('title', 'text', array('label' => 'Titel'))
                ->add('description', 'textarea', array('label' => 'Beschreibung'))
            ->end()
            ->with('Datum und Uhrzeit', array('class' => 'col-md-6'))
                ->add('dateTime', 'datetime', array('label' => 'Start'))
                ->add('hasTime', 'checkbox', array('label' => 'Uhrzeit anzeigen?'))
            ->end()
            ->with('Treffpunkt', array('class' => 'col-md-6'))
                ->add('hasLocation', 'checkbox', array('label' => 'Treffpunkt anzeigen?'))
                ->add('location', 'text', array('label' => 'Treffpunkt'))
                ->add('latitude', 'text', array('label' => 'Breitengrad'))
                ->add('longitude', 'text', array('label' => 'Längengrad'))
            ->end()
            ->with('GPX-Track', array('class' => 'col-md-6'))
                ->add('optimizedGpxContent', 'textarea', array('label' => 'GPX-Inhalt'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('city')
            ->add('location')
            ->add('dateTime')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->addIdentifier('city')
            ->addIdentifier('hasLocation')
            ->addIdentifier('location')
            ->addIdentifier('hasTime')
        ;
    }
}