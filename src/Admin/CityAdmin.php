<?php declare(strict_types=1);

namespace App\Admin;

use App\Criticalmass\RideNamer\RideNamerListInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CityAdmin extends AbstractAdmin
{
    /** @var RideNamerListInterface $rideNamerList */
    protected $rideNamerList;

    public function __construct(string $code, string $class, string $baseControllerName, RideNamerListInterface $rideNamerList)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->rideNamerList = $rideNamerList;
    }

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
            ->add('rideNamer', ChoiceType::class, [
                'choices' => $this->rideNamerList->getList(),
            ])
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
            ->add('imageFile', VichImageType::class, ['required' => false])
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
