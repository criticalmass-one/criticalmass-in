<?php

namespace Criticalmass\Bundle\AppBundle\Form\Type;

use Criticalmass\Bundle\AppBundle\EntityInterface\SocialNetworkInterface;
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
        $networkList = $this->getNetworkList();

        $builder
            ->add('identifier', TextType::class, ['required' => false])
            ->add('network', ChoiceType::class, ['required' => false, 'choices' => $networkList])
            ->add('mainNetwork', CheckboxType::class, ['required' => false])
        ;
    }

    public function getName(): string
    {
        return 'social_network_profile';
    }

    protected function getNetworkList(): array
    {
        $networkList = $this->networkManager->getNetworkList();

        var_dump($networkList);
        return $networkList;
    }
}
