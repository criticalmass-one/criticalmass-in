<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SocialNetworkTwigExtension extends AbstractExtension
{
    const INTRO_LENGTH = 350;

    public function __construct(private readonly NetworkManagerInterface $networkManager)
    {

    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('network_icon', [$this, 'networkIcon']),
            new TwigFunction('getNetwork', [$this, 'getNetwork'], ['is_safe' => ['html']]),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('trim_intro', [$this, 'trimIntro']),
        ];
    }

    public function trimIntro(string $text): string
    {
        $text = strip_tags($text);
        $textLength = strlen($text);

        if ($textLength > self::INTRO_LENGTH) {
            $additionalLength = self::INTRO_LENGTH;

            while ($additionalLength < $textLength - 1) {
                ++$additionalLength;

                if (in_array($text[$additionalLength], ['.', ';', '!', '?', '…'])) {
                    break;
                }
            }

            return substr($text, 0, $additionalLength + 1);
        }

       return $text;
    }

    public function networkIcon($param): string
    {
        if ($param instanceof SocialNetworkFeedItem) {
            $networkIdentifier = $param->getSocialNetworkProfile()->getNetwork();
        } elseif ($param instanceof SocialNetworkProfile) {
            $networkIdentifier = $param->getNetwork();
        } elseif (is_string($param)) {
            $networkIdentifier = $param;
        } else {
            throw new \InvalidArgumentException('Parameter must be instance of SocialNetworkFeedItem or SocialNetworkProfile or a string identifying the network.');
        }

        /** @var NetworkInterface $network */
        $network = $this->networkManager->getNetworkList()[$networkIdentifier];

        return $network->getIcon();
    }

    public function getNetwork(string $identifier): ?NetworkInterface
    {
        if (!$this->networkManager->hasNetwork($identifier)) {
            return null;
        }

        return $this->networkManager->getNetwork($identifier);
    }
    
}