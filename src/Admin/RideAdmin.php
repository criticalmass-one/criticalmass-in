<?php declare(strict_types=1);

namespace App\Admin;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\DBAL\Type\RideDisabledReasonType;
use App\Entity\City;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RideAdmin extends AbstractAdmin
{
    /** @var ObjectRouterInterface $objectRouter */
    protected $objectRouter;

    public function __construct($code, $class, $baseControllerName, ObjectRouterInterface $objectRouter)
    {
        $this->objectRouter = $objectRouter;

        parent::__construct($code, $class, $baseControllerName);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $disabledReasonList = array_flip(RideDisabledReasonType::$choices);
        $disabledReasonList[null] = null;

        $formMapper
            ->with('Details', ['class' => 'col-md-6'])
            ->add('city', EntityType::class, ['class' => City::class, 'required' => true])
            ->add('slug', TextType::class, ['required' => false])
            ->add('title', TextType::class, ['required' => true])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['rows' => 3]])
            ->end()
            ->with('Einstellungen', ['class' => 'col-md-6'])
            ->add('restrictedPhotoAccess', CheckboxType::class, ['required' => false])
            ->add('enabled', CheckboxType::class, ['required' => false])
            ->add('disabledReason', ChoiceType::class, [
                'choices' => $disabledReasonList,
                'required' => 'false',
            ])
            ->add('disabledReasonMessage', TextareaType::class, ['required' => false, 'attr' => ['rows' => 3]])
            ->end()
            ->with('Social Media', ['class' => 'col-md-6'])
            ->add('imageFile', VichImageType::class, ['required' => false])
            ->add('socialDescription', TextareaType::class, ['required' => false])
            ->end()
            ->with('Uhrzeit', ['class' => 'col-md-6'])
            ->add('dateTime', DateTimeType::class, ['widget' => 'single_text', 'required' => true])
            ->end()
            ->with('Treffpunkt', ['class' => 'col-md-6'])
            ->add('location', TextType::class, ['required' => false])
            ->add('latitude', NumberType::class, ['required' => false])
            ->add('longitude', NumberType::class, ['required' => false])
            ->end()
            ->with('Statistik', ['class' => 'col-md-6'])
            ->add('estimatedParticipants', NumberType::class, ['required' => false])
            ->add('estimatedDuration', NumberType::class, ['required' => false])
            ->add('estimatedDistance', NumberType::class, ['required' => false])
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('dateTime')
            ->add('city')
            ->add('title')
            ->add('description')
            ->add('enabled')
            ->add('disabledReason')
            ->add('location');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('dateTime')
            ->add('title')
            ->add('city')
            ->add('location')
            ->add('enabled')
            ->add('_action', 'actions', [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                ]
            ]);
    }

    public function generateObjectUrl($name, $object, array $parameters = [], $absolute = false): string
    {
        if ('show' === $name) {
            return $this->objectRouter->generate($object);
        }
        $parameters['id'] = $this->getUrlsafeIdentifier($object);
        return $this->generateUrl($name, $parameters, $absolute);
    }

    public function getActionButtons($action, $object = null): array
    {
        $list = parent::getActionButtons($action, $object);

        if ('edit' === $action) {
            $list['view'] = [
                'template' => 'Admin/view_button.html.twig',
            ];
        }

        return $list;
    }
}
