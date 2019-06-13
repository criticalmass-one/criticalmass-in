<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

final class BlogPostAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('createdAt')
            ->add('enabled')
            ->add('text')
            ->add('intro')
            ->add('imageName')
            ->add('imageSize')
            ->add('imageMimeType')
            ;
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('createdAt')
            ->add('enabled')
            ->add('text')
            ->add('intro')
            ->add('imageName')
            ->add('imageSize')
            ->add('imageMimeType')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('createdAt')
            ->add('enabled')
            ->add('text')
            ->add('intro')
            ->add('imageName')
            ->add('imageSize')
            ->add('imageMimeType')
            ;
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('id')
            ->add('title')
            ->add('slug')
            ->add('createdAt')
            ->add('enabled')
            ->add('text')
            ->add('intro')
            ->add('imageName')
            ->add('imageSize')
            ->add('imageMimeType')
            ;
    }
}
