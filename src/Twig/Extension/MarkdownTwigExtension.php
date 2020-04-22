<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Embed\Embedder\EmbedderInterface;
use Flagception\Manager\FeatureManagerInterface;
use League\CommonMark\CommonMarkConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MarkdownTwigExtension extends AbstractExtension
{
    protected EmbedderInterface $embedder;
    protected FeatureManagerInterface $featureManager;

    public function __construct(EmbedderInterface $embedder, FeatureManagerInterface $featureManager)
    {
        $this->embedder = $embedder;
        $this->featureManager = $featureManager;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('markdown', [$this, 'markdown'], ['is_safe' => ['html']]),
        ];
    }

    public function markdown(string $text): string
    {
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $text = $converter->convertToHtml($text);

        if ($this->featureManager->isActive('oembed')) {
            $text = $this->embedder->processEmbedsInText($text);
        }

        return $text;
    }

    public function getName(): string
    {
        return 'markdown_extension';
    }
}
