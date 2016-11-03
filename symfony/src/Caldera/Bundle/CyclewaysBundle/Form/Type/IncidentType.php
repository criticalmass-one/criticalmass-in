<?php

namespace Caldera\Bundle\CyclewaysBundle\Form\Type;

use Caldera\Bundle\CalderaBundle\Entity\Incident;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class IncidentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('required' => false))
            ->add('description', TextareaType::class, array('required' => false))
            ->add('polyline', HiddenType::class)
            ->add('geometryType', HiddenType::class)
            ->add('incidentType', ChoiceType::class,
                [
                    'choices' => [
                        'Road Rage' => Incident::INCIDENT_RAGE,
                        'Gefahrenstelle' => Incident::INCIDENT_DANGER,
                        'Arbeitsstelle' => Incident::INCIDENT_ROADWORKS,
                        'Unfall' => Incident::INCIDENT_ACCIDENT,
                        'Tödlicher Unfall' => Incident::INCIDENT_DEADLY_ACCIDENT,
                        'Polizeikontrolle' => Incident::INCIDENT_POLICE
                    ]
                ]
            )
            ->add('visibleFrom', DateType::class)
            ->add('visibleTo', DateType::class)
            ->add('expires', CheckboxType::class)
            ->add('street', TextType::class)
            ->add('houseNumber', TextType::class)
            ->add('suburb', TextType::class)
            ->add('district', TextType::class)
            ->add('zipCode', HiddenType::class)
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class);
    }

    public function getName()
    {
        return 'incident';
    }
}