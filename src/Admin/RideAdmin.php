<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RideAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Details', ['class' => 'col-md-6'])
            ->add('city')
            ->add('slug')
            ->add('title')
            ->add('description')
            ->add('restrictedPhotoAccess')
            ->end()

            ->with('Social Media', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->add('socialDescription', TextareaType::class, ['required' => false])
            ->end()

            ->with('Uhrzeit', ['class' => 'col-md-6'])
            ->add('hasTime')
            ->add('dateTime')
            ->end()

            ->with('Treffpunkt', ['class' => 'col-md-6'])
            ->add('hasLocation')
            ->add('location')
            ->add('latitude')
            ->add('longitude')
            ->end()

            ->with('Statistik', ['class' => 'col-md-6'])
            ->add('estimatedParticipants')
            ->add('estimatedDuration')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('dateTime')
            ->add('title')
            ->add('description')
            ->add('location');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->add('title')
            ->add('city')
            ->add('location');
    }
}
