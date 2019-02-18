<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class Twitter extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'twitter';

    /** @var string $icon */
    protected $icon = 'fa-twitter';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(29, 161, 242)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/http(?:s)?:\/\/(?:www\.)?twitter\.com\/([a-zA-Z0-9_]+)/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && 2 === count($matches);
    }
}
