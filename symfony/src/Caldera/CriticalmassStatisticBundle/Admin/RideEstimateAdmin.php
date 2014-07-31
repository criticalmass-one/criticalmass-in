<?php

namespace Caldera\CriticalmassStatisticBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RideEstimateAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('ride', null, array())
                ->add('user', null, array())
                ->add('creationDateTime', null, array())
            ->end()
            ->with('Statistik', array('class' => 'col-md-6'))
                ->add('estimatedParticipants', 'text', array('label' => 'ungefähre Teilnehmerzahl'))
                ->add('estimatedDistance', 'text', array('label' => 'ungefähre Fahrstrecke'))
                ->add('estimatedDuration', 'text', array('label' => 'ungefähre Fahrdauer'))
            ->end();
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('estimatedParticipants')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('estimatedParticipants')
        ;
    }
}