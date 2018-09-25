<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Website\Crawler\Crawlable;
use App\Criticalmass\Website\Crawler\CrawlerInterface;
use App\Entity\BlacklistedWebsite;
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

    protected $blacklistPatternList = [];

    public function __construct(RegistryInterface $registry, CrawlerInterface $crawler, EngineInterface $twigEngine)
    {
        $this->registry = $registry;

        $this->crawler = $crawler;

        $this->twigEngine = $twigEngine;

        $this->populateBlacklist();
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
            if (!$this->checkIfBlacklisted($url)) {
                $crawledWebsite = $this->registry->getRepository(CrawledWebsite::class)->findOneByUrl($url);

                if ($crawledWebsite) {
                    $message = str_replace($url, $this->twigEngine->render('Crawler/_website.html.twig', ['website' => $crawledWebsite]), $message);
                }
            }
        }

        return $message;
    }

    protected function populateBlacklist(): void
    {
        $this->blacklistPatternList = $this->registry->getRepository(BlacklistedWebsite::class)->findAll();
    }

    protected function checkIfBlacklisted(string $url): ?BlacklistedWebsite
    {
        foreach ($this->blacklistPatternList as $blacklistPattern) {
            if (preg_match($blacklistPattern->getPattern(), $url)) {
                return $blacklistPattern;
            }
        }

        return null;
    }

    public function getName(): string
    {
        return 'crawl_extension';
    }
}

