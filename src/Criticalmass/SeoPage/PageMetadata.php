<?php declare(strict_types=1);

namespace App\Criticalmass\SeoPage;

/**
 * Holds the <head> metadata of the current page: title, <meta> tags grouped by
 * attribute type (name, property, http-equiv, charset), attributes for the
 * <html> and <head> elements, the canonical link and hreflang alternates.
 *
 * The defaults are wired in config/services.yaml; controllers adjust the
 * values per page through SeoPageInterface.
 */
final class PageMetadata
{
    /** @var array<string, array<string, string>> */
    private array $metas;

    private string $linkCanonical = '';

    /** @var array<string, string> href => hreflang */
    private array $langAlternates = [];

    /**
     * @param array<string, array<string, string>> $metas          type => [name => content]
     * @param array<string, string>                $htmlAttributes
     * @param array<string, string>                $headAttributes
     */
    public function __construct(
        private string $title = '',
        array $metas = [],
        private array $htmlAttributes = [],
        private array $headAttributes = [],
    ) {
        $this->metas = $metas;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Adds or replaces a meta tag; an existing entry keeps its position.
     */
    public function addMeta(string $type, string $name, string $content): self
    {
        $this->metas[$type][$name] = $content;

        return $this;
    }

    /** @return array<string, array<string, string>> */
    public function getMetas(): array
    {
        return $this->metas;
    }

    public function getMeta(string $type, string $name): ?string
    {
        return $this->metas[$type][$name] ?? null;
    }

    public function addHtmlAttribute(string $name, string $value): self
    {
        $this->htmlAttributes[$name] = $value;

        return $this;
    }

    /** @return array<string, string> */
    public function getHtmlAttributes(): array
    {
        return $this->htmlAttributes;
    }

    public function addHeadAttribute(string $name, string $value): self
    {
        $this->headAttributes[$name] = $value;

        return $this;
    }

    /** @return array<string, string> */
    public function getHeadAttributes(): array
    {
        return $this->headAttributes;
    }

    public function setLinkCanonical(string $link): self
    {
        $this->linkCanonical = $link;

        return $this;
    }

    public function getLinkCanonical(): string
    {
        return $this->linkCanonical;
    }

    public function addLangAlternate(string $href, string $hrefLang): self
    {
        $this->langAlternates[$href] = $hrefLang;

        return $this;
    }

    /** @return array<string, string> */
    public function getLangAlternates(): array
    {
        return $this->langAlternates;
    }
}
