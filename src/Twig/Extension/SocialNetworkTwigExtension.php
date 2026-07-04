<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
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
        if ($param instanceof SocialNetworkProfile) {
            $networkIdentifier = $param->getNetwork();
        } elseif (is_string($param)) {
            $networkIdentifier = $param;
        } elseif (null === $param) {
            // Templates pass e.g. item.socialNetworkProfile.network, which is
            // null for a feed item whose profile was removed (#1305).
            $networkIdentifier = null;
        } else {
            throw new \InvalidArgumentException('Parameter must be instance of SocialNetworkProfile or a string identifying the network.');
        }

        // A removed profile yields a null identifier (#1305); an unknown network
        // (e.g. one the Feeds API no longer lists) falls back to a generic icon
        // instead of crashing on getIcon().
        if (null === $networkIdentifier || !$this->networkManager->hasNetwork($networkIdentifier)) {
            return 'far fa-globe';
        }

        return $this->networkManager->getNetwork($networkIdentifier)->getIcon();
    }

    public function getNetwork(string $identifier): ?NetworkInterface
    {
        if (!$this->networkManager->hasNetwork($identifier)) {
            return null;
        }

        return $this->networkManager->getNetwork($identifier);
    }

}
