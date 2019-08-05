<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class BlogPostAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('user')
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
            ->add('user')
            ->add('title')
            ->add('createdAt')
            ->add('enabled')
            ->add('intro')
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
            ->with('Title', ['class' => 'col-md-6'])
            ->add('title')
            ->add('slug')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('createdAt')
            ->add('enabled')
            ->add('user')
            ->end()

            ->with('Text', ['class' => 'col-md-6'])
            ->add('text')
            ->add('intro')
            ->end()

            ->with('Header', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->end();
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
