<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class InstagramProfile extends AbstractInstagramNetwork
{
    protected string $name = 'Instagram-Profil';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(instagram\.([a-zA-Z]{2,3}))\/([a-zA-Z0-9-]{5,})(\/?)$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
