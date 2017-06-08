<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Track', ['class' => 'col-md-6'])
            ->add('ride')
            ->add('user')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('enabled')
            ->add('deleted')
            ->end()

            ->with('Track file', ['class' => 'col-md-6'])
            ->add('trackFile', VichFileType::class)
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('enabled')
            ->add('deleted')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('creationDateTime')
            ->add('enabled')
            ->add('deleted')
        ;
    }
}
