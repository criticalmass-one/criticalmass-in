<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

class ThreadsProfile extends AbstractNetwork
{
    protected string $name = 'Threads-Profil';

    protected string $icon = 'fab fa-threads';

    protected string $backgroundColor = '#000000';

    protected string $textColor = 'white';

    public function accepts(string $url): bool
    {
        $patterns = [
            '/^https?:\/\/(www\.)?threads\.(net|com)\/@[\w.]+\/?$/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, trim($url))) {
                return true;
            }
        }

        return false;
    }
}
