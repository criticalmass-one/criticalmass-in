<?php declare(strict_types=1);

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CityAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Städteinformationen', ['class' => 'col-md-6'])
            ->add('city')
            ->add('cityPopulation')
            ->end()
            ->with('Critical Mass', ['class' => 'col-md-6'])
            ->add('title')
            ->add('description')
            ->add('longdescription', TextType::class)
            ->add('punchline', TextType::class)
            ->end()
            ->with('Geografie', ['class' => 'col-md-6'])
            ->add('region')
            ->add('latitude')
            ->add('longitude')
            ->end()
            ->with('Technisches', ['class' => 'col-md-6'])
            ->add('mainSlug')
            ->add('timezone')
            ->end()
            ->with('Soziale Netzwerke', ['class' => 'col-md-6'])
            ->add('url')
            ->add('facebook')
            ->add('twitter')
            ->add('enableBoard')
            ->end()
            ->with('Headergrafik', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class)
            ->end()
            ->with('Wiederkehrende Touren', ['class' => 'col-md-6'])
            ->add('isStandardable')
            ->add('standardDayOfWeek')
            ->add('standardWeekOfMonth')
            ->add('isStandardableTime')
            ->add('standardTime')
            ->add('isStandardableLocation')
            ->add('standardLocation')
            ->add('standardLatitude')
            ->add('standardLongitude')
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('title')
            ->add('description');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('title');
    }
}
