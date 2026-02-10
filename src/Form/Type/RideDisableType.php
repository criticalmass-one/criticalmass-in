<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Enum\RideDisabledReasonEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class RideDisableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('disabledReason', EnumType::class, [
            'class' => RideDisabledReasonEnum::class,
            'required' => true,
            'expanded' => true,
            'multiple' => false,
        ])
            ->add('disabledReasonMessage', TextareaType::class, [
                'required' => false,
            ]);
    }

    public function getName(): string
    {
        return 'ride_disable';
    }
}
