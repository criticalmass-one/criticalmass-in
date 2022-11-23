<?php declare(strict_types=1);

namespace App\Form\Type;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SocialNetworkProfileEditType extends AbstractType
{
    /** @var NetworkManagerInterface $networkManager */
    protected $networkManager;

    public function __construct(NetworkManagerInterface $networkManager)
    {
        $this->networkManager = $networkManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, ['required' => true, 'disabled' => true])
            ->add('mainNetwork', CheckboxType::class, ['required' => false])
            ->add('network', ChoiceType::class, [
                'choices' => $this->getNetworkList(),
            ]);
    }

    protected function getNetworkList(): array
    {
        $list = [];

        /** @var NetworkInterface $network */
        foreach ($this->networkManager->getNetworkList() as $network) {
            $list[$network->getName()] = $network->getIdentifier();
        }

        return $list;
    }

    public function getName(): string
    {
        return 'social_network_profile';
    }
}
