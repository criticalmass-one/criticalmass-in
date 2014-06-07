<?php

namespace Caldera\CriticalmassCoreBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class CommentAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('user', null, array('label' => 'Teilnehmer'))
                ->add('ride', null, array('label' => 'Tour'))
                ->add('enabled', 'checkbox', array('label' => 'aktiviert'))
            ->end()
            ->with('Geografie', array('class' => 'col-md-6'))
                ->add('latitude', 'text', array('label' => 'Breitengrad'))
                ->add('longitude', 'text', array('label' => 'Längengrad'))
            ->end()
            ->with('Kommentar', array('class' => 'col-md-6'))
                ->add('dateTime', 'datetime', array('label' => 'Datum und Uhrzeit'))
                ->add('message', 'textarea', array('label' => 'Kommentar'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('message')
            ->add('dateTime')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('user')
            ->add('ride')
            ->add('message')
            ->add('dateTime')
        ;
    }
}