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
            )),
            new \Twig_SimpleFilter('hashtagToCity', [$this, 'hashtagToCity'], array(
                'is_safe' => array('html')
            )),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('metadata', [$this, 'getMetadataService',], array(
                'is_safe' => array('raw')
            )),
            new \Twig_SimpleFunction('gravatarHash', [$this, 'gravatarHash'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('gravatarUrl', [$this, 'gravatarUrl'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('daysSince', [$this, 'daysSince'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('twitterUsername', [$this, 'twitterUsername'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('facebookIdentifier', [$this, 'facebookIdentifier'], array(
                'is_safe' => array('html')
            )),
            new \Twig_SimpleFunction('hostname', [$this, 'hostname'], array(
                'is_safe' => array('html')
            ))
        ];
    }

    public function getTests()
    {
        return [
            'instanceof' => new \Twig_Function_Method($this, 'isInstanceof'),
            'today' => new \Twig_Function_Method($this, 'today')
        ];
    }

    public function markdown($text)
    {
        $parsedown = new \Parsedown();

        $text = $parsedown->parse($text);

        return $text;
    }

    public function gravatarHash(User $user = null)
    {
        if (!$user) {
            return 'avatar';
        }
        
        return md5($user->getEmail());
    }

    public function gravatarUrl(User $user = null, $size = 64)
    {
        return 'http://www.gravatar.com/avatar/' . $this->gravatarHash($user) . '?s=' . $size;
    }

    public function daysSince($dateTimeString)
    {
        $dateTime = new \DateTime($dateTimeString);
        $now = new \DateTime();

        $diffSeconds = $now->getTimestamp() - $dateTime->getTimestamp();

        $diffDays = floor($diffSeconds / (60 * 60 * 24));

        return $diffDays;
    }

    public function isInstanceof($var, $instance)
    {
        return $var instanceof $instance;
    }

    public function today(\DateTime $dateTime)
    {
        $today = new \DateTime();

        return ($today->format('Y-m-d') == $dateTime->format('Y-m-d'));
    }

    public function getMetadataService()
    {
        return $this->container->get('caldera.criticalmass.metadata');
    }

    public function twitterUsername($twitterUrl)
    {
        $parsedParts = parse_url($twitterUrl);

        $username = $parsedParts['path'];

        $username = str_replace('/', '', $username);

        $username = '@' . $username;

        return $username;
    }

    public function facebookIdentifier($facebookUrl)
    {
        $parsedParts = parse_url($facebookUrl);

        $identifier = $parsedParts['path'];

        $identifier = str_replace('/', '', $identifier);

        return $identifier;
    }

    public function hostname($url)
    {
        $parsedParts = parse_url($url);

        $hostname = $parsedParts['host'];

        $hostname = str_replace('www.', '', $hostname);

        return $hostname;
    }

    public function hashtagToCity($string)
    {
        preg_match_all('/#([a-zA-Z0-9]*)/', $string, $result);

        foreach ($result[0] as $hashtag) {
            $lcHashtag = strtolower($hashtag);

            $citySlug = substr($lcHashtag, 1, strlen($hashtag) - 1);

            $path = $this->container->get('router')->generate('caldera_criticalmass_desktop_city_show', ['citySlug' => $citySlug]);

            $link = '<a href="'.$path.'">'.$hashtag.'</a>';
            
            $string = str_replace($hashtag, $link, $string);
        }

        return $string;
    }

    public function getName()
    {
        return 'site_extension';
    }
}

