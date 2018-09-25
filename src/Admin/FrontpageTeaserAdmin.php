<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class FrontpageTeaserAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Teaser', ['class' => 'col-md-6'])
            ->add('headline', TextType::class, ['required' => false])
            ->add('text', TextareaType::class, ['required' => false])
            ->add('position', NumberType::class, ['required' => true])
            ->end()
            ->with('Photo', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->end()
            ->with('Valid range', ['class' => 'col-md-6'])
            ->add('validFrom', DateTimeType::class, ['widget' => 'single_text', 'required' => false])
            ->add('validUntil', DateTimeType::class, ['widget' => 'single_text', 'required' => false])
            ->end()
            ->with('Buttons', ['class' => 'col-md-6'])
            ->add('buttons')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('headline')
            ->add('text');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('headline');
    }
}
