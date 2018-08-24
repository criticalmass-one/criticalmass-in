<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('colorRed', NumberType::class)
            ->add('colorGreen', NumberType::class)
            ->add('colorBlue', NumberType::class);
    }

    public function getName(): string
    {
        return 'profile_color_type';
    }
}
