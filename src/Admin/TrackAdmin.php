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
use Vich\UploaderBundle\Form\Type\VichFileType;

class TrackAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Track', ['class' => 'col-md-6'])
            ->add('ride', EntityType::class, ['class' => Ride::class, 'required' => true])
            ->add('user', EntityType::class, ['class' => User::class, 'required' => true])
            ->end()

            ->with('Settings', ['class' => 'col-md-6'])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('deleted', CheckboxType::class, ['required' => false])
            ->end()

            ->with('Track file', ['class' => 'col-md-6'])
            ->add('trackFile', VichFileType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('user')
            ->add('ride')
            ->add('enabled')
            ->add('deleted');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id')
            ->add('user')
            ->add('ride')
            ->add('creationDateTime')
            ->add('enabled')
            ->add('deleted');
    }
}
