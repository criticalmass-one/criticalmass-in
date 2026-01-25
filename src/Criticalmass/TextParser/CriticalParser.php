<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\TextParser\Embedder\EmbedderInterface;
use App\Criticalmass\TextParser\EmbedExtension\EmbedExtension;
use App\Criticalmass\TextParser\TextCache\TextCacheInterface;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;

class CriticalParser implements TextParserInterface
{
    private readonly ConverterInterface $converter;

    public function __construct(
        private readonly FeatureManagerInterface $featureManager,
        private readonly EmbedderInterface $embedder,
        private readonly TextCacheInterface $textCache
    )
    {
        $this->configure();
    }

    protected function configure(): void
    {
        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);

        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AutolinkExtension());

        if ($this->featureManager->isActive('oembed')) {
            $environment->addExtension(new EmbedExtension($this->embedder));
        }

        $this->converter = new CommonMarkConverter($config);
    }

    public function parse(string $text): string
    {
        if ($this->textCache->has($text)) {
            $cached = $this->textCache->get($text);
            return is_string($cached) ? $cached : $cached->getContent();
        }

        $parsed = $this->converter->convert($text);
        $content = $parsed->getContent();

        $this->textCache->set($text, $content);

        return $content;
    }
}
