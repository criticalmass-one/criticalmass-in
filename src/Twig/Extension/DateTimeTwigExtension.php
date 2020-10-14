<?php declare(strict_types=1);

namespace App\Twig\Extension;

class DateTimeTwigExtension extends \Twig_Extension
{
    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('date_time_add', [$this, 'add'], [
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

