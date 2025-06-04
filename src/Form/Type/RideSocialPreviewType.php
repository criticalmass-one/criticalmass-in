<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RideSocialPreviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('socialDescription', TextareaType::class, [
                'required' => false,
            ])
            ->add('imageFile', VichFileType::class, [ // @TODO use VichImageType after getting liip filters working
                'required' => false,
                'allow_delete' => true,
                'download_uri' => true,
            ]);
    }

    public function getName(): string
    {
        return 'ride_social';
    }
}
