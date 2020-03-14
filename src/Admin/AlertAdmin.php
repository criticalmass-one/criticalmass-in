<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AlertAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Inhalt', ['class' => 'col-md-6'])
            ->add('title', TextType::class, ['required' => true])
            ->add('message', TextareaType::class, ['required' => true])
            ->add('type', ChoiceType::class, ['choices' => [
                'danger' => 'danger',
                'warning' => 'warning',
                'success' => 'success',
                'info' => 'info',
            ]])
            ->end()
            ->with('Zeitraum', ['class' => 'col-md-6'])
            ->add('fromDateTime', DateTimeType::class, ['widget' => 'single_text', 'required' => false])
            ->add('untilDateTime', DateTimeType::class, ['widget' => 'single_text', 'required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('message')
            ->add('type')
            ->add('fromDateTime')
            ->add('untilDateTime');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title')
            ->add('message')
            ->add('type')
            ->add('fromDateTime')
            ->add('untilDateTime');
    }
}
