<?php declare(strict_types=1);

namespace App\Twig\Extension;

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

    public function add(\DateTime $dateTime, string $dateIntervalSpec): \DateTime
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
