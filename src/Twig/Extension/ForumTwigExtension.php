<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Forum\RelativeTime;
use App\Criticalmass\Forum\SearchSnippet;
use App\Criticalmass\Router\PostUrlGenerator;
use App\Entity\Post;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ForumTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly SearchSnippet $searchSnippet,
        private readonly RelativeTime $relativeTime,
        private readonly PostUrlGenerator $postUrlGenerator
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('post_url', [$this, 'postUrl']),
        ];
    }

    public function postUrl(Post $post): string
    {
        return $this->postUrlGenerator->generate($post);
    }

    public function getFilters(): array
    {
        return [
            // is_safe, weil SearchSnippet den Text selbst maskiert und nur die
            // Markierungen als HTML einfügt.
            new TwigFilter('search_snippet', [$this, 'snippet'], ['is_safe' => ['html']]),
            new TwigFilter('time_ago', [$this, 'timeAgo']),
        ];
    }

    public function snippet(?string $text, string $term): string
    {
        return $this->searchSnippet->build($text, $term);
    }

    public function timeAgo(?\DateTimeInterface $dateTime): string
    {
        return $this->relativeTime->format($dateTime);
    }
}
