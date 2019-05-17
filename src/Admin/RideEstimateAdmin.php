<?php declare(strict_types=1);

namespace App\Admin;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class RideEstimateAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('RideEstimate', ['class' => 'col-md-6'])
            ->add('estimatedParticipants', NumberType::class, ['required' => false])
            ->add('estimatedDistance', NumberType::class, ['required' => false])
            ->add('estimatedDuration', NumberType::class, ['required' => false])
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('user', EntityType::class, ['class' => User::class, 'required' => false])
            ->add('ride', EntityType::class, ['class' => Ride::class])
            ->add('track', EntityType::class, ['class' => Track::class, 'required' => false])
            ->add('dateTime', DateTimeType::class, [
                'date_widget' => 'single_text',
                'date_format' => 'dd.MM.yyyy',
                'time_widget' => 'single_text',
                'compound' => true,
            ])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('track')
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('estimatedParticipants')
            ->add('estimatedDistance')
            ->add('estimatedDuration')
            ->add('creationDateTime');
    }
}
