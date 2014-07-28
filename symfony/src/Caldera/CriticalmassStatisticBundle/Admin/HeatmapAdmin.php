<?php

namespace Caldera\CriticalmassStatisticBundle\Admin;

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
            ->end()
            ->with('Identifier', array('class' => 'col-md-6'))
                ->add('Identifier', 'text', array('label' => 'Identifier'))
            ->end()
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('public', 'checkbox', array('label' => 'Öffentlich sichtbar?'))
                ->add('cities', 'sonata_type_model', array('label' => 'Verknüpfte Städte', 'multiple' => true, 'btn_add' => false))
            ->end();
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