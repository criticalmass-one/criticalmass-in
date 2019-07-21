<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Website\Crawler\Crawlable;
use App\Criticalmass\Website\Crawler\CrawlerInterface;
use App\Criticalmass\Website\Obfuscator\ObfuscatorInterface;
use App\Entity\BlacklistedWebsite;
use App\Entity\CrawledWebsite;
use Flagception\Manager\FeatureManagerInterface;
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

    /** @var ObfuscatorInterface $obfuscator */
    protected $obfuscator;

    /** @var FeatureManagerInterface $featureManager */
    protected $featureManager;

    /** @var array $blacklistPatternList */
    protected $blacklistPatternList = [];

    public function __construct(RegistryInterface $registry, CrawlerInterface $crawler, EngineInterface $twigEngine, ObfuscatorInterface $obfuscator, FeatureManagerInterface $featureManager)
    {
        $this->registry = $registry;
        $this->crawler = $crawler;
        $this->twigEngine = $twigEngine;
        $this->obfuscator = $obfuscator;
        $this->featureManager = $featureManager;

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
                    if ($this->featureManager->isActive('art11')) {
                        $crawledWebsite = $this->obfuscateWebsite($crawledWebsite);
                    }

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

    protected function obfuscateWebsite(CrawledWebsite $crawledWebsite): CrawledWebsite
    {
        $crawledWebsite
            ->setTitle($this->obfuscate($crawledWebsite->getTitle()))
            ->setDescription($this->obfuscate($crawledWebsite->getDescription()));

        return $crawledWebsite;
    }

    protected function obfuscate(string $text = null): ?string
    {
        if (!$text) {
            return null;
        }

        return $this->obfuscator->obfuscate($text);
    }

    public function getName(): string
    {
        return 'crawl_extension';
    }
}

