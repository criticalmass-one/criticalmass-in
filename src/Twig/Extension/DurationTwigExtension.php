<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Khill\Duration\Duration;

class DurationTwigExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('human_duration', [$this, 'duration'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function duration(string $duration = null): ?string
    {
        if (!$duration) {
            return null;
        }

        return (new Duration())->humanize($duration);
    }

    public function getName(): string
    {
        return 'duration_extension';
    }
}

