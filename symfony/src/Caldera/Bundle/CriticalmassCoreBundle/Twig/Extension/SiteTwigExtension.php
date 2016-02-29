<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Twig\Extension;

use Caldera\Bundle\CriticalmassModelBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SiteTwigExtension extends \Twig_Extension
{
    private $translator;

    private $container;

    public function __construct(TranslatorInterface $translator, ContainerInterface $container)
    {
        $this->translator = $translator;
        $this->container = $container;
    }

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdown', [$this, 'markdown'], array(
                'is_safe' => array('html')
            ))
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('gravatarHash', [$this, 'gravatarHash'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('gravatarUrl', [$this, 'gravatarUrl'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('daysSince', [$this, 'daysSince'], array(
                'is_safe' => array('html')
            ))
        ];
    }

    public function getTests()
    {
        return [
            'instanceof' =>  new \Twig_Function_Method($this, 'isInstanceof'),
            'today' => new \Twig_Function_Method($this, 'today')
        ];
    }

    public function markdown($text)
    {
        $parsedown = new \Parsedown();

        $text = $parsedown->parse($text);

        return $text;
    }

    public function gravatarHash(User $user)
    {
        return md5($user->getEmail());
    }

    public function gravatarUrl(User $user, $size = 64)
    {
        return 'http://www.gravatar.com/avatar/'.$this->gravatarHash($user).'?s='.$size;
    }

    public function daysSince($dateTimeString)
    {
        $dateTime = new \DateTime($dateTimeString);
        $now = new \DateTime();

        $diffSeconds = $now->getTimestamp() - $dateTime->getTimestamp();

        $diffDays = floor($diffSeconds / (60 * 60 * 24));

        return $diffDays;
    }

    public function isInstanceof($var, $instance) {
        return $var instanceof $instance;
    }

    public function today(\DateTime $dateTime) {
        $today = new \DateTime();

        return ($today->format('Y-m-d') == $dateTime->format('Y-m-d'));
    }

    public function getName()
    {
        return 'site_extension';
    }
}

