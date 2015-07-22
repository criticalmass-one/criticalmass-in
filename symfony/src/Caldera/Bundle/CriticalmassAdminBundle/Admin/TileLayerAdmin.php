<?php

namespace Caldera\CriticalmassCoreBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class TileLayerAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Beschreibung', array('class' => 'col-md-6'))
                ->add('title', 'text', array('label' => 'Titel'))
                ->add('description', 'textarea', array('label' => 'Beschreibung'))
            ->end()
            ->with('Karte', array('class' => 'col-md-6'))
                ->add('address', 'text', array('label' => 'Adresse'))
                ->add('attributation', 'text', array('label' => 'Attributation'))
            ->end()
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('active', 'checkbox', array('label' => 'aktiv'))
                ->add('public', 'checkbox', array('label' => 'öffentlich sichtbar'))
                ->add('plusOnly', 'checkbox', array('label' => 'nur für Plus-Mitglieder'))
                ->add('standard', 'checkbox', array('label' => 'Standard-Karte'))
            ->end();
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('description')
            ->add('address')
            ->add('attributation')
            ->add('public')
            ->add('active')
            ->add('plusOnly')
            ->add('standard')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->addIdentifier('address')
            ->add('public')
            ->add('active')
            ->add('plusOnly')
            ->add('standard')
        ;
    }
}