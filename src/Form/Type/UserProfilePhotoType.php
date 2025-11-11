<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class UserProfilePhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imageFile',
                VichFileType::class, [
                    'required' => false,
                    'allow_delete' => false,
                ]
            );
    }

    public function getName(): string
    {
        return 'user_photo';
    }
}
