<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use App\Criticalmass\TextParser\EmbedExtension\EmbedExtension;
use App\Criticalmass\TextParser\TextCache\TextCacheInterface;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\MarkdownConverterInterface;

class CriticalParser implements TextParserInterface
{
    protected MarkdownConverterInterface $converter;

    public function __construct(
        private FeatureManagerInterface $featureManager,
        private EmbedderInterface $embedder,
        private TextCacheInterface $textCache
    )
    {
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

    public function parse(string $text): string
    {
        $parsedText = null;

        if ($this->textCache->has($text)) {
            $parsedText = $this->textCache->get($text);
        } else {
            $parsedText = $this->converter->convert($text);
            $this->textCache->set($text, $parsedText->getContent());
        }

        if (!is_string($parsedText)) {
            return $parsedText->getContent();
        }

        return $parsedText;
    }
}