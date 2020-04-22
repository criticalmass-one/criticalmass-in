<?php declare(strict_types=1);

namespace App\Criticalmass\TextParser;

use App\Criticalmass\Embed\Embedder\EmbedderInterface;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;

class CriticalParser implements TextParserInterface
{
    protected EmbedderInterface $embedder;
    protected FeatureManagerInterface $featureManager;

    public function __construct(EmbedderInterface $embedder, FeatureManagerInterface $featureManager)
    {
        $this->embedder = $embedder;
        $this->featureManager = $featureManager;
    }

    public function parse(string $text): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => true,
        ]);

        $text = $converter->convertToHtml($text);

        if ($this->featureManager->isActive('oembed')) {
            $text = $this->embedder->processEmbedsInText($text);
        }

        return $text;
    }
}