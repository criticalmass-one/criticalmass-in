<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\SeoPage\PageMetadata;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Renders the PageMetadata into the <head>. Output format matches what the
 * previously used SonataSeoBundle produced, so the markup is unchanged.
 */
class PageMetadataTwigExtension extends AbstractExtension
{
    public function __construct(private readonly PageMetadata $page)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('seo_title', $this->title(...), ['is_safe' => ['html']]),
            new TwigFunction('seo_metadatas', $this->metadatas(...), ['is_safe' => ['html']]),
            new TwigFunction('seo_html_attributes', $this->htmlAttributes(...), ['is_safe' => ['html']]),
            new TwigFunction('seo_head_attributes', $this->headAttributes(...), ['is_safe' => ['html']]),
            new TwigFunction('seo_link_canonical', $this->linkCanonical(...), ['is_safe' => ['html']]),
            new TwigFunction('seo_lang_alternates', $this->langAlternates(...), ['is_safe' => ['html']]),
        ];
    }

    public function title(): string
    {
        return sprintf('<title>%s</title>', strip_tags($this->page->getTitle()));
    }

    public function metadatas(): string
    {
        $html = '';

        foreach ($this->page->getMetas() as $type => $metas) {
            foreach ($metas as $name => $content) {
                $html .= '' !== $content
                    ? sprintf("<meta %s=\"%s\" content=\"%s\" />\n", $type, $this->normalize($name), $this->normalize($content))
                    : sprintf("<meta %s=\"%s\" />\n", $type, $this->normalize($name));
            }
        }

        return $html;
    }

    public function htmlAttributes(): string
    {
        return $this->attributes($this->page->getHtmlAttributes());
    }

    public function headAttributes(): string
    {
        return $this->attributes($this->page->getHeadAttributes());
    }

    public function linkCanonical(): string
    {
        if ('' === $this->page->getLinkCanonical()) {
            return '';
        }

        return sprintf("<link rel=\"canonical\" href=\"%s\"/>\n", $this->page->getLinkCanonical());
    }

    public function langAlternates(): string
    {
        $html = '';

        foreach ($this->page->getLangAlternates() as $href => $hrefLang) {
            $html .= sprintf("<link rel=\"alternate\" href=\"%s\" hreflang=\"%s\"/>\n", $href, $hrefLang);
        }

        return $html;
    }

    /** @param array<string, string> $attributes */
    private function attributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $name => $value) {
            $html .= sprintf('%s="%s" ', $name, $value);
        }

        return rtrim($html);
    }

    private function normalize(string $string): string
    {
        return htmlentities(strip_tags($string), \ENT_COMPAT, 'UTF-8');
    }
}
