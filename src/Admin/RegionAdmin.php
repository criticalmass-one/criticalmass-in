<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\Region;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegionAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Region', ['class' => 'col-md-6'])
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, ['required' => false])
            ->end()

            ->with('Wikidata', ['class' => 'col-md-6'])
            ->add('wikidataEntityId', TextType::class, ['required' => false])
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('slug', TextType::class)
            ->add('parent', EntityType::class, ['class' => Region::class, 'required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('description')
            ->add('parent')
            ->add('wikidataEntityId');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('name')
            ->add('parent')
            ->add('wikidataEntityId');
    }
}
