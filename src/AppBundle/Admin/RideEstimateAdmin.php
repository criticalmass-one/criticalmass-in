<?php

namespace AppBundle\Admin;

use AppBundle\Entity\Ride;
use AppBundle\Entity\Track;
use AppBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RideEstimateAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('RideEstimate', ['class' => 'col-md-6'])
            ->add('estimatedParticipants', NumberType::class, ['required' => false])
            ->add('estimatedDistance', NumberType::class, ['required' => false])
            ->add('estimatedDuration', NumberType::class, ['required' => false])
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('user', EntityType::class, ['class' => User::class])
            ->add('ride', EntityType::class, ['class' => Ride::class])
            ->add('track', EntityType::class, ['class' => Track::class, 'required' => false])
            ->add('creationDateTime')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('track')
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration')
            ->add('creationDateTime');
    }
}
