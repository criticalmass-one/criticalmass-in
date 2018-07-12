<?php

namespace App\Form\Type;

use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SocialNetworkProfileType extends AbstractType
{
    protected $networkManager;

    public function __construct(NetworkManager $networkManager)
    {
        $this->networkManager = $networkManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, ['required' => false])
            ->add('mainNetwork', CheckboxType::class, ['required' => false]);
    }

    public function getName(): string
    {
        return 'social_network_profile';
    }
}
