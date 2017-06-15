<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LocationAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Location', ['class' => 'col-md-6'])
            ->add('title')
            ->add('description')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('city')
            ->add('slug')
            ->end()

            ->with('Coords', ['class' => 'col-md-6'])
            ->add('latitude')
            ->add('longitude')
            ->end()
        ;

    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('city')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('city')
        ;
    }
}
