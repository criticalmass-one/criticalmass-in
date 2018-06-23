<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class BikerightVoucherAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Voucher', ['class' => 'col-md-6'])
            ->add('code')
            ->add('priority')
            ->add('user')
            ->end()
            ->with('Date', ['class' => 'col-md-6'])
            ->add('createdAt')
            ->add('assignedAt')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('code')
            ->add('priority')
            ->add('user')
            ->add('createdAt')
            ->add('assignedAt');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('code')
            ->add('priority')
            ->add('user')
            ->add('assignedAt');
    }
}
