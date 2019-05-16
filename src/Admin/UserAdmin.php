<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('User data', ['class' => 'col-md-6'])
            ->add('username')
            ->add('email')
            ->add('plainPassword', TextType::class, ['required' => false])
            ->end()
            ->with('Color', ['class' => 'col-md-6'])
            ->add('colorRed')
            ->add('colorGreen')
            ->add('colorBlue')
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('blurGalleries', CheckboxType::class, ['required' => false])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('ownProfilePhoto', CheckboxType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('createdAt')
            ->add('lastLogin')
            ->add('enabled');
    }
}
