<?php declare(strict_types=1);

namespace App\Admin;

use App\DBAL\Type\RideDisabledReasonType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RideAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $disabledReasonList = array_flip(RideDisabledReasonType::$choices);
        $disabledReasonList[null] = 'foo';

        $formMapper
            ->with('Details', ['class' => 'col-md-6'])
            ->add('city')
            ->add('slug')
            ->add('title')
            ->add('description')
            ->end()

            ->with('Einstellungen', ['class' => 'col-md-6'])
            ->add('restrictedPhotoAccess')
            ->add('enabled')
            ->add('disabledReason', ChoiceType::class, [
                'choices' => $disabledReasonList,
            ])
            ->end()

            ->with('Social Media', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->add('socialDescription', TextareaType::class, ['required' => false])
            ->end()

            ->with('Uhrzeit', ['class' => 'col-md-6'])
            ->add('dateTime')
            ->end()

            ->with('Treffpunkt', ['class' => 'col-md-6'])
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
            ->add('enabled')
            ->add('location');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->add('title')
            ->add('city')
            ->add('location')
            ->add('enabled');
    }
}
