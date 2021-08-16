<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CriticalmassBlogExtension extends AbstractExtension
{
    protected CriticalmassBlogInterface $criticalmassBlog;

    public function __construct(CriticalmassBlogInterface $criticalmassBlog)
    {
        $this->criticalmassBlog = $criticalmassBlog;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('blog_articles', [$this, 'getBlogArticles']),
        ];
    }

    public function getBlogArticles(): array
    {
        return $this->criticalmassBlog->getArticles();
    }
}
