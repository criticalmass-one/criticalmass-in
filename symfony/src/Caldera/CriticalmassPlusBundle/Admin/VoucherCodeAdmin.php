<?php

namespace Caldera\CriticalmassPlusBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class VoucherCodeAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('voucherClass', null, array('label' => 'Gutschein-Klasse'))
                ->add('code', 'text', array('label' => 'Code'))
            ->end()
            ->with('Benutzer', array('class' => 'col-md-6'))
                ->add('user', null, array('label' => 'Benutzer'))
                ->add('activationDateTime', 'datetime', array('label' => 'Aktivierung'))
            ->end();
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('code')
            ->add('user')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->addIdentifier('user')
        ;
    }
}