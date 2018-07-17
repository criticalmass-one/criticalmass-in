<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Website\Crawler\Crawlable;
use \simplehtmldom_1_5\simple_html_dom as HtmlDomElement;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CrawlTwigExtension extends \Twig_Extension
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('parse_urls', [$this, 'parseUrls']),
        ];
    }

    public function parseUrls(Crawlable $crawlable): string
    {

    }

    public function getName(): string
    {
        return 'crawl_extension';
    }
}

