<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\Embed\Embedder\EmbedderInterface;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;

class CriticalParser implements TextParserInterface
{
    protected EmbedderInterface $embedder;
    protected FeatureManagerInterface $featureManager;
    protected ConverterInterface $converter;

    public function __construct(EmbedderInterface $embedder, FeatureManagerInterface $featureManager)
    {
        $this->embedder = $embedder;
        $this->featureManager = $featureManager;

        $this->configure();
    }

    protected function configure(): void
    {
        $environment = Environment::createCommonMarkEnvironment();

        $environment->addExtension(new AutolinkExtension());

        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $this->converter = new CommonMarkConverter($config, $environment);
    }

    public function parse(string $text): string
    {
        $text = $this->converter->convertToHtml($text);

        if ($this->featureManager->isActive('oembed')) {
            $text = $this->embedder->processEmbedsInText($text);
        }

        return $text;
    }
}