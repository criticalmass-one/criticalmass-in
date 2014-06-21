<?php

namespace Caldera\CriticalmassPlusBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class VoucherClassAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('title', 'text', array('label' => 'Bezeichnung'))
                ->add('description', 'textarea', array('label' => 'Beschreibung'))
                ->add('codePrefix', 'text', array('label' => 'Code-Prefix'))
            ->end()
            ->with('Zeitraum', array('class' => 'col-md-6'))
                ->add('validSince', 'datetime', array('label' => 'gültig von'))
                ->add('validUntil', 'datetime', array('label' => 'gültig bis'))
            ->end();
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('description')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->addIdentifier('description')
        ;
    }
}