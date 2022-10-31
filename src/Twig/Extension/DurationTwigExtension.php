<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Khill\Duration\Duration;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DurationTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('human_duration', $this->duration(...), [
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
