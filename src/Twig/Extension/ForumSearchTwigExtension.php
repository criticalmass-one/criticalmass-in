<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Forum\SearchSnippet;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ForumSearchTwigExtension extends AbstractExtension
{
    public function __construct(private readonly SearchSnippet $searchSnippet)
    {
    }

    public function getFilters(): array
    {
        return [
            // is_safe, weil SearchSnippet den Text selbst maskiert und nur die
            // Markierungen als HTML einfügt.
            new TwigFilter('search_snippet', [$this, 'snippet'], ['is_safe' => ['html']]),
        ];
    }

    public function snippet(?string $text, string $term): string
    {
        return $this->searchSnippet->build($text, $term);
    }
}
