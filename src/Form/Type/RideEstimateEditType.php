<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RideEstimateEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('estimatedParticipants', TextType::class, ['required' => true])
            ->add('dateTime', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
            ]);
    }

    public function getName(): string
    {
        return 'ride_estimate_edit';
    }
}
