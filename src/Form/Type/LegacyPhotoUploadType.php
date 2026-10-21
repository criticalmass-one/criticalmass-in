<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class LegacyPhotoUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('imageFile', VichImageType::class, [
            'download_uri' => false,
            'image_uri' => false,
            'delete_label' => 'Bild entfernen',
        ]);
    }

    public function getName(): string
    {
        return 'photo_legacy';
    }
}
