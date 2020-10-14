<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class InstagramPhoto extends AbstractInstagramNetwork
{
    protected string $name = 'Instagram-Foto';

    public function accepts(string $url): bool
    {
        $pattern = '/^(https?\:\/\/)?(www\.)?(instagram\.com)\/p\/.+$/';

        preg_match($pattern, $url, $matches);

        return $matches && is_array($matches) && count($matches) > 1;
    }
}
