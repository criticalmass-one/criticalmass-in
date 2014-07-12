<?php

namespace Caldera\CriticalmassContentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ContentItemAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Inhalt', array('class' => 'col-md-6'))
                ->add('title', 'text', array('label' => 'Titel'))
                ->add('slug', 'text', array('label' => 'Slug'))
                ->add('text', 'textarea', array('label' => 'Inhalt'))
                ->add('enabled', 'checkbox', array('label' => 'Aktiviert'))
                ->add('positionOrder', 'text', array('label' => 'Position'))
                ->add('contentClass', null, array('label' => 'Content-Klasse'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
        ;
    }
}