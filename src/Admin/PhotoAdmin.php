<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\Ride;
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
use Vich\UploaderBundle\Form\Type\VichImageType;

class PhotoAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Photo', ['class' => 'col-md-6'])
            ->add('ride', EntityType::class, ['class' => Ride::class])
            ->add('user', EntityType::class, ['class' => User::class])
            ->add('description', TextareaType::class, ['required' => false])
            ->end()

            ->with('Date', ['class' => 'col-md-6'])
            ->add('dateTime', DateTimeType::class)
            ->add('creationDateTime', DateTimeType::class)
            ->end()

            ->with('Coords', ['class' => 'col-md-6'])
            ->add('latitude', TextType::class, ['required' => false])
            ->add('longitude', TextType::class, ['required' => false])
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('deleted', CheckboxType::class, ['required' => false])
            ->end()

            ->with('Image file', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('views')
            ->add('enabled')
            ->add('deleted');
    }
}
