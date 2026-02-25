<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DurationTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('human_duration', [$this, 'duration'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function duration(?string $duration = null): ?string
    {
        if (!$duration) {
            return null;
        }

        $totalSeconds = (int) $duration;
        $hours = intdiv($totalSeconds, 3600);
        $minutes = intdiv($totalSeconds % 3600, 60);

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . "\u{00A0}" . 'h';
        }
        if ($minutes > 0 || $hours === 0) {
            $parts[] = $minutes . "\u{00A0}" . 'min';
        }

        return implode(' ', $parts);
    }

    public function getName(): string
    {
        return 'duration_extension';
    }
}
