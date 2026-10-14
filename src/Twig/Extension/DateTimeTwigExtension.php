<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Symfony\Component\Clock\ClockInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateTimeTwigExtension extends AbstractExtension
{
    public function __construct(private readonly ClockInterface $clock)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('date_time_add', [$this, 'add'], [
                'is_safe' => ['html'],
            ]),
            new TwigFunction('is_today', [$this, 'isToday']),
        ];
    }

    public function isToday(?\DateTimeInterface $dateTime, ?string $timezone = null): bool
    {
        if (!$dateTime) {
            return false;
        }

        $zone = new \DateTimeZone($timezone ?: 'UTC');
        $now = $this->clock->now()->setTimezone($zone);
        $then = \DateTimeImmutable::createFromInterface($dateTime)->setTimezone($zone);

        return $now->format('Y-m-d') === $then->format('Y-m-d');
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
