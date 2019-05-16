<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\Thread;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Post', ['class' => 'col-md-6'])
            ->add('user', EntityType::class, ['class' => User::class, 'required' => true])
            ->add('message', TextareaType::class, ['required' => false])
            ->end()

            ->with('Context', ['class' => 'col-md-6'])
            ->add('ride', EntityType::class, ['class' => Ride::class, 'required' => false])
            ->add('city', EntityType::class, ['class' => City::class, 'required' => false])
            ->add('thread', EntityType::class, ['class' => Thread::class, 'required' => false])
            //->add('photo') too much data, admin will explode
            ->end()

            ->with('Coord', ['class' => 'col-md-6'])
            ->add('latitude', TextType::class, ['required' => false])
            ->add('longitude', TextType::class, ['required' => false])
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('dateTime', DateTimeType::class, ['required' => false])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('user')
            ->add('message')
            ->add('enabled');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('message')
            ->add('user')
            ->add('dateTime')
            ->add('enabled');
    }
}
