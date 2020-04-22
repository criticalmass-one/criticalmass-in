<?php declare(strict_types=1);

namespace App\Criticalmass\Embed\LinkFinder;

class LinkFinder implements LinkFinderInterface
{
    const REGEX_PATTERN = '/(?:(?:https?:\/\/)?[\w-]+(?:\.[a-z-]+)+\.?(?::\d+)?(?:\/[a-z0-9_\-\/,.?=#]*)?)/im';

    public function findInText(string $text): array
    {
        preg_match_all(self::REGEX_PATTERN, $text, $matches);

        return array_pop($matches);
    }
}