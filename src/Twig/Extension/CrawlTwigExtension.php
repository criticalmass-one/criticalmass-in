<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Website\Crawler\Crawlable;
use App\Criticalmass\Website\Crawler\CrawlerInterface;
use App\Entity\CrawledWebsite;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class CrawlTwigExtension extends \Twig_Extension
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var CrawlerInterface $crawler */
    protected $crawler;

    /** @var EngineInterface $twigEngine */
    protected $twigEngine;

    public function __construct(RegistryInterface $registry, CrawlerInterface $crawler, EngineInterface $twigEngine)
    {
        $this->registry = $registry;

        $this->crawler = $crawler;

        $this->twigEngine = $twigEngine;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('parse_urls', [$this, 'parseUrls'], ['is_safe' => ['html']]),
        ];
    }

    public function parseUrls(Crawlable $crawlable): string
    {
        $urls = $this->crawler->crawlUrls($crawlable);

        $message = $crawlable->getText();

        foreach ($urls as $url) {
            $crawledWebsite = $this->registry->getRepository(CrawledWebsite::class)->findOneByUrl($url);

            $message = str_replace($url, $this->twigEngine->render('Crawler/_website.html.twig', ['website' => $crawledWebsite]), $message);
        }

        return $message;
    }

    public function getName(): string
    {
        return 'crawl_extension';
    }
}

