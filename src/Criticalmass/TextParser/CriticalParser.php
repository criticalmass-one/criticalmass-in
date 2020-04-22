<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\TextParser\EmbedExtension\EmbedExtension;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\MarkdownConverterInterface;

class CriticalParser implements TextParserInterface
{
    protected FeatureManagerInterface $featureManager;
    protected MarkdownConverterInterface $converter;

    public function __construct(FeatureManagerInterface $featureManager)
    {
        $this->featureManager = $featureManager;

        $this->configure();
    }

    protected function configure(): void
    {
        $environment = Environment::createCommonMarkEnvironment();

        if ($this->featureManager->isActive('oembed')) {
            $environment->addExtension(new EmbedExtension());
        }

        $environment->addExtension(new AutolinkExtension());

        $config = [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ];

        $this->converter = new CommonMarkConverter($config, $environment);
    }

    public function parse(string $text): string
    {
        return $this->converter->convertToHtml($text);
    }
}