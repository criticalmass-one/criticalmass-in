<?php

namespace Caldera\Bundle\CriticalmassAdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ArticleAdmin extends Admin
{
    // Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Inhalt', array('class' => 'col-md-6'))
                ->add('title', 'text', array('label' => 'Titel'))
                ->add('abstract', 'textarea', array('label' => 'Abstrakt'))
                ->add('text', 'textarea', array('label' => 'Text'))
            ->end()
            ->with('Einstellungen', array('class' => 'col-md-6'))
                ->add('dateTime', 'datetime', array('label' => 'Start'))
                ->add('user', null, array('label' => 'Autor'))
            ->end()
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('abstract')
            ->add('text')
            ->add('dateTime')
            ->add('user')
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title')
            ->addIdentifier('abstract')
            ->addIdentifier('text')
            ->addIdentifier('dateTime')
            ->addIdentifier('user')
        ;
    }
}