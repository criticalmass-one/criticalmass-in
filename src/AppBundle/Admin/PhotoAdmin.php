<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Vich\UploaderBundle\Form\Type\VichImageType;

class PhotoAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Photo', ['class' => 'col-md-6'])
            ->add('ride')
            ->add('user')
            ->add('description')
            ->end()

            ->with('Date', ['class' => 'col-md-6'])
            ->add('dateTime')
            ->add('creationDateTime')
            ->end()

            ->with('Coords', ['class' => 'col-md-6'])
            ->add('latitude')
            ->add('longitude')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('enabled')
            ->add('deleted')
            ->end()

            ->with('Image file', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class)
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('views')
            ->add('enabled')
            ->add('deleted')
        ;
    }
}
