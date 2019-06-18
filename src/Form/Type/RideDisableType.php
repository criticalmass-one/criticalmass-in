<?php declare(strict_types=1);

namespace App\Form\Type;

use App\DBAL\Type\RideDisabledReasonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class RideDisableType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('disabledReason', ChoiceType::class, [
            'required' => true,
            'choices' => array_flip(RideDisabledReasonType::$choices),
            'expanded' => true,
            'multiple' => false,
        ]);
    }

    public function getName(): string
    {
        return 'ride_disable';
    }
}
