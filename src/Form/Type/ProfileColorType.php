<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('color', ColorType::class, [
            'label' => 'form.profile_color.label',
        ]);
    }

    public function getName(): string
    {
        return 'profile_color_type';
    }
}
