<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\Board;
use App\Entity\City;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ThreadAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Thread', ['class' => 'col-md-6'])
            ->add('title')
            ->end()
            ->with('Context', ['class' => 'col-md-6'])
            ->add('board', EntityType::class, ['class' => Board::class, 'required' => false])
            ->add('city', EntityType::class, ['class' => City::class, 'required' => false])
            ->end()
            ->with('Settings', ['class' => 'col-md-6'])
            ->add('slug')
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('firstPost.user')
            ->add('board')
            ->add('city')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title')
            ->add('firstPost.user')
            ->add('firstPost.dateTime')
            ->add('board')
            ->add('city')
            ->add('enabled');
    }
}
