<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use App\Criticalmass\TextParser\EmbedExtension\EmbedExtension;
use App\Criticalmass\TextParser\TextCache\TextCacheInterface;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\MarkdownConverterInterface;

class CriticalParser implements TextParserInterface
{
    protected FeatureManagerInterface $featureManager;
    protected MarkdownConverterInterface $converter;
    protected EmbedderInterface $embedder;
    protected TextCacheInterface $textCache;

    public function __construct(FeatureManagerInterface $featureManager, EmbedderInterface $embedder, TextCacheInterface $textCache)
    {
        $this->featureManager = $featureManager;
        $this->embedder = $embedder;
        $this->textCache = $textCache;

        $this->configure();
    }

    protected function configure(): void
    {
        $environment = Environment::createCommonMarkEnvironment();

        if ($this->featureManager->isActive('oembed')) {
            $environment->addExtension(new EmbedExtension($this->embedder));
        }

        $environment->addExtension(new AutolinkExtension());

        $config = [
            'html_input' => '',
            'allow_unsafe_links' => false,
        ];

        $this->converter = new CommonMarkConverter($config, $environment);
    }

    public function parse(string $rawText): string
    {
        $parsedText = null;

        if ($this->textCache->has($rawText)) {
            $parsedText = $this->textCache->get($rawText);
        } else {
            $parsedText = $this->converter->convertToHtml($rawText);
            $this->textCache->set($rawText, $parsedText);
        }

        return $parsedText;
    }
}