<?php declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SocialNetworkProfileAddType extends SocialNetworkProfileType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'required' => true,
            ])
            ->add('mainNetwork', CheckboxType::class, [
                'required' => false,
            ])
            ->add('network', ChoiceType::class, [
                'choices' => $this->getNetworkList(),
                'placeholder' => '(automatisch erkennen)',
                'required' => false,
            ]);
    }
}
