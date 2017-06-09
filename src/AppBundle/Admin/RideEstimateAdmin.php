<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class RideEstimateAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('RideEstimate', ['class' => 'col-md-6'])
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('user')
            ->add('ride')
            ->add('track')
            ->add('creationDateTime')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('track')
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration')
            ->add('creationDateTime')
        ;
    }
}
