<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Carbon\Carbon;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateTimeTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('date_time_add', [$this, 'add'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function add(Carbon $dateTime, string $dateIntervalSpec): Carbon
    {
        $dateInterval = new \DateInterval($dateIntervalSpec);

        $dateTime = clone $dateTime;
        
        return $dateTime->add($dateInterval);
    }

    public function getName(): string
    {
        return 'datetime_extension';
    }
}
