<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ThreadAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Thread', ['class' => 'col-md-6'])
            ->add('title')
            ->end()

            ->with('Context', ['class' => 'col-md-6'])
            ->add('board')
            ->add('city')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('slug')
            ->add('enabled')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('firstPost.user')
            ->add('board')
            ->add('city')
            ->add('enabled')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->add('firstPost.user')
            ->add('firstPost.dateTime')
            ->add('board')
            ->add('city')
            ->add('enabled')
        ;
    }
}
