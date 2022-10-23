<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SiteTwigExtension extends AbstractExtension
{
    public function __construct(protected TranslatorInterface $translator, protected RouterInterface $router)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('hashtagToCity', [$this, 'hashtagToCity'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('daysSince', $this->daysSince(...), ['is_safe' => ['html']]),
            new TwigFunction('today', $this->today(...), ['is_safe' => ['html']]),
            'instanceof' => new TwigFunction('instanceof', $this->instanceof(...)),
            'today' => new TwigFunction('today', $this->today(...))
        ];
    }

    public function daysSince($dateTimeString): float
    {
        $dateTime = new \DateTime($dateTimeString);
        $now = new \DateTime();

        $diffSeconds = $now->getTimestamp() - $dateTime->getTimestamp();

        $diffDays = floor($diffSeconds / (60 * 60 * 24));

        return $diffDays;
    }

    public function instanceof ($var, $instance): bool
    {
        return $var instanceof $instance;
    }

    public function today(\DateTime $dateTime): bool
    {
        $today = new \DateTime();

        return ($today->format('Y-m-d') == $dateTime->format('Y-m-d'));
    }

    public function getName(): string
    {
        return 'site_extension';
    }
}
