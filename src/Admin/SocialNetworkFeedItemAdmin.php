<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SocialNetworkFeedItemAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Feed Item', ['class' => 'col-md-6'])
            ->add('title', TextType::class, ['required' => true])
            ->add('text', TextareaType::class, ['required' => true])
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('dateTime', DateTimeType::class, ['required' => true])
            ->add('createdAt', DateTimeType::class, ['required' => true])
            ->add('hidden', CheckboxType::class)
            ->add('deleted', CheckboxType::class)
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('text')
            ->add('dateTime')
            ->add('hidden')
            ->add('deleted')
            ->add('createdAt');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title')
            ->add('dateTime')
            ->add('hidden')
            ->add('deleted');
    }
}
