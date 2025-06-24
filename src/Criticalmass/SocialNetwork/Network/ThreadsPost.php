<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class ThreadsPost extends AbstractNetwork
{
    protected string $name = 'Threads-Beitrag';

    protected string $icon = 'fab fa-threads';

    protected string $backgroundColor = '#000000';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/(www\.)?threads\.net\/@[\w.]+\/post\/[0-9]+\/?$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, trim($url))) {
                return true;
            }
        }

        return false;
    }
}
