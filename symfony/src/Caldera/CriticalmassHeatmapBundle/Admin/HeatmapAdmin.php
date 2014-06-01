<?php

namespace Caldera\CriticalmassHeatmapBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class HeatmapAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Beschreibung', array('class' => 'col-md-6'))
                ->add('title', 'text', array('label' => 'Titel'))
                ->add('description', 'textarea', array('label' => 'Beschreibung'))
            ->end()
            ->with('Touren', array('class' => 'col-md-6'))
                ->add('rides', 'sonata_type_model', array('label' => 'Touren', 'multiple' => true, 'btn_add' => false))
            ->end();
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
        ;
    }
}