<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BoardAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Board', ['class' => 'col-md-6'])
            ->add('title')
            ->add('description')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('slug')
            ->add('position')
            ->add('enabled')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('threadNumber')
            ->add('postNumber')
            ->add('position')
            ->add('enabled')
        ;
    }
}
