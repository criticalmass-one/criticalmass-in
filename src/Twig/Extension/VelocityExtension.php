<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\Track;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class VelocityExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('average_velocity', [$this, 'averageVelocity']),
        ];
    }

    public function averageVelocity(Track $track): ?float
    {
        if ($track->getStartDateTime() && $track->getEndDateTime() && $track->getDistance()) {
            return null;
        }

        $kilometres = $track->getDistance();
        $seconds = $track->getEndDateTime()->getTimestamp() - $track->getStartDateTime()->getTimestamp();

        $hours = (float)$seconds / 3600;

        $velocity = $kilometres / ($hours + 0.0001);

        return $velocity;
    }

    public function getName(): string
    {
        return self::class;
    }
}
