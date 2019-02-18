<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;

class Tumblr extends AbstractNetwork
{
    /** @var string $name */
    protected $name = 'Tumblr';

    /** @var string $icon */
    protected $icon = 'fa-tumblr';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(0, 0, 0)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        $pattern = '/^(https?\:\/\/)((www\.)?)([a-zA-Z0-9]*)\.(tumblr\.com)(\/?)$/';

        preg_match($pattern, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
