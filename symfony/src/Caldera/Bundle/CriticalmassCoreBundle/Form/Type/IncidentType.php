<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Form\Type;

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
                    'choices'  => [
                        0 => Incident::INCIDENT_RAGE,
                        1 => Incident::INCIDENT_DANGER,
                        2 => Incident::INCIDENT_ROADWORKS
                    ]
                ]
            )
            ->add('visibleFrom', DateType::class)
            ->add('visibleTo', DateType::class)
            ->add('expires', CheckboxType::class);
    }

    public function getName()
    {
        return 'incident';
    }
}