<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class FeedItemIntroTwigExtension extends AbstractExtension
{
    const INTRO_LENGTH = 350;

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

            while ($additionalLength < $textLength) {
                ++$additionalLength;

                if (in_array($text[$additionalLength], ['.', ';', '!', '?', '…'])) {
                    break;
                }
            }

            return substr($text, 0, $additionalLength + 1);
        }

       return $text;
    }
}