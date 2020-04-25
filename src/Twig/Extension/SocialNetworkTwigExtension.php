<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SocialNetworkTwigExtension extends AbstractExtension
{
    protected NetworkManagerInterface $networkManager;

    public function __construct(NetworkManagerInterface $networkManager)
    {
        $this->networkManager = $networkManager;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getNetwork', [$this, 'getNetwork'], ['is_safe' => ['html']]),
        ];
    }

    public function getName(): string
    {
        return 'social_network_extension';
    }

    public function getNetwork(string $identifier): ?NetworkInterface
    {
        if (!$this->networkManager->hasNetwork($identifier)) {
            return null;
        }

        return $this->networkManager->getNetwork($identifier);
    }
}

