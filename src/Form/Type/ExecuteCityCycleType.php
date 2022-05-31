<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class ExecuteCityCycleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fromDate', DateType::class, [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'required' => true,
            ])
            ->add('untilDate', DateType::class, [
                'model_timezone' => 'UTC',
                'view_timezone' => 'UTC',
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'required' => true,
            ]);
    }

    public function getName(): string
    {
        return 'city_cycle_execute';
    }
}
