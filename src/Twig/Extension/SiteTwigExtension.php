<?php declare(strict_types=1);

namespace App\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SiteTwigExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;
    protected RouterInterface $router;

    public function __construct(TranslatorInterface $translator, RouterInterface $router)
    {
        $this->translator = $translator;
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('daysSince', [$this, 'daysSince'], array(
                'is_safe' => array('html')
            )),
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

    public function getName(): string
    {
        return 'site_extension';
    }
}
