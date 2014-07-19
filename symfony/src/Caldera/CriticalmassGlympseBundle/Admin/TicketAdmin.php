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
                ->add('creationDateTime', 'datetime', array('label' => 'Datum'))
                ->add('runtime', 'text', array('label' => 'Laufzeit'))
                ->add('counter', 'text', array('label' => 'Zähler'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('inviteId')
            ->add('city')
            ->add('creationDateTime')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('inviteId')
            ->add('city')
            ->add('creationDateTime')
        ;
    }
}