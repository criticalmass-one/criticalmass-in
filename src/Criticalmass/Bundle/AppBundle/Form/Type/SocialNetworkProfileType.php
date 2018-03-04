<?php

namespace Criticalmass\Bundle\AppBundle\Form\Type;

use Criticalmass\Bundle\AppBundle\EntityInterface\SocialNetworkInterface;
use Criticalmass\Component\SocialNetwork\Network\NetworkInterface;
use Criticalmass\Component\SocialNetwork\NetworkManager\NetworkManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            ->add('network', ChoiceType::class, [
                'required' => false,
                'choices' => $this->networkManager->getNetworkList(),
                'choice_label' => function (NetworkInterface $value, string $key, int $index) {
                    return $value->getName();
                },
            ])
            ->add('mainNetwork', CheckboxType::class, ['required' => false]);
    }

    public function getName(): string
    {
        return 'social_network_profile';
    }
}
