<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractFacebookNetwork extends AbstractNetwork
{
    const REGEX_PATTERN = '/(?:(?:http|https):\/\/)?(?:www.)?facebook.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[?\w\-]*\/)?(?:profile.php\?id=(?=\d.*))?([\w\-]*)?/';

    /** @var string $icon */
    protected $icon = 'fab fa-facebook-f';

    /** @var string $backgroundColor */
    protected $backgroundColor = 'rgb(60, 88, 152)';

    /** @var string $textColor */
    protected $textColor = 'white';

    public function accepts(string $url): bool
    {
        preg_match(self::REGEX_PATTERN, $url, $matches);

        return $matches && is_array($matches) && 2 === count($matches);
    }

    protected function getProfileFromUrl(string $url): ?string
    {
        preg_match(self::REGEX_PATTERN, $url, $matches);

        if (!$matches || !is_array($matches) || 2 !== count($matches)) {
            return null;
        }

        return array_pop($matches);
    }
}
