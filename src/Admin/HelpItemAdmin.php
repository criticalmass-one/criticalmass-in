<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\HelpCategory;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class HelpItemAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Content', ['class' => 'col-md-6'])
            ->add('title', TextType::class)
            ->add('text', TextAreaType::class)
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('category', EntityType::class, [
                'class' => HelpCategory::class
            ])
            ->add('position', NumberType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('category')
            ->add('title')
            ->add('text');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title')
            ->add('category');
    }
}
