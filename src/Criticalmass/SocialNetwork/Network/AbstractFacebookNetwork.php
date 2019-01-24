<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;
use App\Criticalmass\Facebook\Api\FacebookApi;

abstract class AbstractFacebookNetwork extends AbstractNetwork
{
    const REGEX_PATTERN = '/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/';

    protected $name = 'facebook';

    protected $icon = 'fa-facebook';

    protected $backgroundColor = 'rgb(59, 90, 153)';

    protected $textColor = 'white';

    /** @var FacebookApi $facebookApi */
    protected $facebookApi;

    public function __construct(FacebookApi $facebookApi)
    {
        $this->facebookApi = $facebookApi;
    }

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        preg_match(self::REGEX_PATTERN, $socialNetworkProfile->getIdentifier(), $matches);

        return $matches && is_array($matches) && 2 === count($matches);
    }

    protected function getProfileFromUrl(SocialNetworkProfile $socialNetworkProfile): ?string
    {
        preg_match(self::REGEX_PATTERN, $socialNetworkProfile->getIdentifier(), $matches);

        if (!$matches || !is_array($matches) || 2 !== count($matches)) {
            return null;
        }

        return array_pop($matches);
    }
}
