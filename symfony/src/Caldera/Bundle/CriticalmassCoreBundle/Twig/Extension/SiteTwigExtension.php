<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Twig\Extension;

use FOS\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
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
            ))
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

    public function getName()
    {
        return 'site_extension';
    }
}

