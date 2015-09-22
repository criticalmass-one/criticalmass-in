<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Michelf\MarkdownExtra;

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

    public function markdown($text)
    {
        $parser = new MarkdownExtra();
        return $parser->transform($text);
    }

    public function getName()
    {
        return 'site_extension';
    }
}

