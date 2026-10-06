<?php declare(strict_types=1);

namespace App\Criticalmass\Forum;

/**
 * Zeitangaben, wie man sie im Gespräch benutzt: „vor 2 Stunden“ statt eines Zeitstempels.
 *
 * Ab einer Woche wird das ungenau und wenig hilfreich — dann steht wieder das Datum da.
 */
class RelativeTime
{
    public function format(?\DateTimeInterface $dateTime, ?\DateTimeInterface $now = null): string
    {
        if (null === $dateTime) {
            return '';
        }

        $now ??= new \DateTimeImmutable();
        $seconds = $now->getTimestamp() - $dateTime->getTimestamp();

        if ($seconds < 0) {
            return $dateTime->format('d.m.Y, H:i');
        }

        if ($seconds < 60) {
            return 'gerade eben';
        }

        $minutes = intdiv($seconds, 60);

        if ($minutes < 60) {
            return $this->plural($minutes, 'Minute', 'Minuten');
        }

        $hours = intdiv($minutes, 60);

        if ($hours < 24) {
            return $this->plural($hours, 'Stunde', 'Stunden');
        }

        $days = intdiv($hours, 24);

        if (1 === $days) {
            return 'gestern';
        }

        if ($days < 7) {
            return $this->plural($days, 'Tag', 'Tagen');
        }

        return 'am ' . $dateTime->format('d.m.Y');
    }

    private function plural(int $amount, string $singular, string $plural): string
    {
        return sprintf('vor %d %s', $amount, 1 === $amount ? $singular : $plural);
    }
}
