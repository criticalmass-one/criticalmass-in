<?php

namespace Criticalmass\Bundle\AppBundle\Twig\Extension;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Criticalmass\Bundle\AppBundle\HtmlMetadata\Metadata;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SiteTwigExtension extends \Twig_Extension
{
    /** @var TranslatorInterface $translator */
    protected $translator;

    /** @var Metadata $metadata */
    protected $metadata;

    /** @var RouterInterface $router */
    protected $router;

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
            )),
            new \Twig_SimpleFunction('today', [$this, 'today'], array(
                'is_safe' => array('html')
            )),
            'instanceof' => new \Twig_SimpleFunction('instanceof', [$this, 'instanceof']),
            'today' => new \Twig_SimpleFunction('today', [$this, 'today'])
        ];
    }

    public function gravatarHash(User $user = null)
    {
        if (!$user) {
            return 'avatar';
        }

        return md5($user->getEmail());
    }

    public function gravatarUrl(User $user = null, $size = 256)
    {
        return 'https://www.gravatar.com/avatar/' . $this->gravatarHash($user) . '?s=' . $size;
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

    public function getName()
    {
        return 'site_extension';
    }
}

