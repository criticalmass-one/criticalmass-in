<?php declare(strict_types=1);

namespace App\Criticalmass\CriticalmassBlog\Twig;

use App\Criticalmass\CriticalmassBlog\CriticalmassBlogInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CriticalmassBlogExtension extends AbstractExtension
{
    public function __construct(protected CriticalmassBlogInterface $criticalmassBlog)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('blog_articles', $this->getBlogArticles(...)),
        ];
    }

    public function getBlogArticles(): array
    {
        return $this->criticalmassBlog->getArticles();
    }
}
