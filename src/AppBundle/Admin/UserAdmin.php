<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('User data', ['class' => 'col-md-6'])
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class)
            ->add('description', TextareaType::class)
            ->end()

            ->with('Color', ['class' => 'col-md-6'])
            ->add('colorRed')
            ->add('colorGreen')
            ->add('colorBlue')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('blurGalleries')
            ->add('enabled')
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('enabled')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('createdAt')
            ->add('lastLogin')
            ->add('enabled')
        ;
    }
}
