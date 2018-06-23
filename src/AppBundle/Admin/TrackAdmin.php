<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Ride;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Track', ['class' => 'col-md-6'])
            ->add('ride', EntityType::class, ['class' => Ride::class])
            ->add('user', EntityType::class, ['class' => User::class])
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('deleted', CheckboxType::class, ['required' => false])
            ->end()
            ->with('Track file', ['class' => 'col-md-6'])
            ->add('trackFile', VichFileType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('enabled')
            ->add('deleted');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('creationDateTime')
            ->add('enabled')
            ->add('deleted');
    }
}
