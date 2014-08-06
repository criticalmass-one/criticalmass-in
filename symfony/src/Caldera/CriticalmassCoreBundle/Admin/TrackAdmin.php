<?php

namespace Caldera\CriticalmassCoreBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TrackAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('ride', null, array('label' => 'Tour'))
                ->add('user', null, array('label' => 'Benutzer'))
                ->add('ticket', null, array('label' => 'Ticket'))
                ->add('username', null, array('label' => 'Benutzername'))
                ->add('creationDateTime', null, array('label' => 'Datum und Uhrzeit'))
                ->add('gpx', 'textarea', array('label' => 'GPX-Inhalt'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('ride')
            ->add('username')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('ride')
            ->addIdentifier('username')
        ;
    }
}