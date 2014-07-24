<?php

namespace Caldera\CriticalmassGlympseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TicketAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Ticket', array('class' => 'col-md-6'))
                ->add('inviteId', 'text', array('label' => 'Invite-ID'))
                ->add('city', null, array('label' => 'Stadt'))
                ->add('counter', 'text', array('label' => 'Zähler'))
                ->add('active', 'checkbox', array('label' => 'Aktiv'))
            ->end()
            ->with('Benutzer', array('class' => 'col-md-6'))
                ->add('username', 'text', array('label' => 'Benutzerkennung'))
                ->add('displayName', 'text', array('label' => 'Anzeigename'))
                ->add('message', 'text', array('label' => 'Mitteilung'))
            ->end()
            ->with('Farben', array('class' => 'col-md-6'))
                ->add('colorRed', 'text', array('label' => 'Rot'))
                ->add('colorGreen', 'text', array('label' => 'Grün'))
                ->add('colorBlue', 'text', array('label' => 'Blau'))
            ->end()
            ->with('Laufzeit', array('class' => 'col-md-6'))
                ->add('startDateTime', 'datetime', array('label' => 'Startzeitpunkt'))
                ->add('endDateTime', 'datetime', array('label' => 'Endzeitpunkt'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('inviteId')
            ->add('city')
            ->add('username')
            ->add('creationDateTime')
            ->add('displayName')
            ->add('active')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('inviteId')
            ->addIdentifier('username')
            ->addIdentifier('city')
            ->add('creationDateTime')
            ->addIdentifier('displayName')
            ->addIdentifier('active')
        ;
    }
}