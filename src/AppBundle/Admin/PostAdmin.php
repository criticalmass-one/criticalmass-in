<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class PostAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Post', ['class' => 'col-md-6'])
            ->add('user')
            ->add('message')
            ->end()

            ->with('Context', ['class' => 'col-md-6'])
            ->add('ride')
            ->add('city')
            ->add('thread')
            //->add('photo') too much data, admin will explode
            ->end()

            ->with('Coord', ['class' => 'col-md-6'])
            ->add('latitude')
            ->add('longitude')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('dateTime')
            ->add('enabled')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('message')
            ->add('enabled')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('message')
            ->add('user')
            ->add('dateTime')
            ->add('enabled')
        ;
    }
}
