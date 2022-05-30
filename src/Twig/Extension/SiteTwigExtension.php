<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Entity\User;
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

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('hashtagToCity', [$this, 'hashtagToCity'], array(
                'is_safe' => array('html')
            )),
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('daysSince', [$this, 'daysSince'], array(
                'is_safe' => array('html')
            )),
            new TwigFunction('today', [$this, 'today'], array(
                'is_safe' => array('html')
            )),
            'instanceof' => new TwigFunction('instanceof', [$this, 'instanceof']),
            'today' => new TwigFunction('today', [$this, 'today'])
        ];
    }

    public function daysSince($dateTimeString)
    {
        $dateTime = new \DateTime($dateTimeString);
        $now = new \DateTime();

        $diffSeconds = $now->getTimestamp() - $dateTime->getTimestamp();

        $diffDays = floor($diffSeconds / (60 * 60 * 24));

        return $diffDays;
    }

    public function instanceof ($var, $instance)
    {
        return $var instanceof $instance;
    }

    public function today(\DateTime $dateTime)
    {
        $today = new \DateTime();

        return ($today->format('Y-m-d') == $dateTime->format('Y-m-d'));
    }

    public function getName()
    {
        return 'site_extension';
    }
}
