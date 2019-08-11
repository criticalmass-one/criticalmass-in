<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\BlogPost;
use App\Factory\BlogPost\BlogPostFactory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
            ->add('title', TextType::class, ['required' => true])
            ->add('slug', TextType::class, ['required' => true])
            ->add('blog')
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('createdAt', DateTimeType::class, [
                'date_widget' => 'single_text',
                'date_format' => 'dd.MM.yyyy',
                'time_widget' => 'single_text',
                'compound' => true,
            ])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('user')
            ->end()

            ->with('Text', ['class' => 'col-md-6'])
            ->add('intro', TextareaType::class, ['required' => false])
            ->add('text', TextareaType::class, ['required' => true])
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

    public function getNewInstance(): BlogPost
    {
        return (new BlogPostFactory())->build();
    }
}
