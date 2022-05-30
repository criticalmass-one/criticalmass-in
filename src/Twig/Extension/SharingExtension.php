<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;
use App\Criticalmass\Sharing\SocialSharerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SharingExtension extends AbstractExtension
{
    protected SocialSharerInterface $sharer;

    public function __construct(SocialSharerInterface $sharer)
    {
        $this->sharer = $sharer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('shareUrl', [$this, 'shareUrl']),
            new TwigFunction('shareLink', [$this, 'shareLink'], ['is_safe' => ['html']]),
            new TwigFunction('shareDropdownLink', [$this, 'shareDropdownLink'], ['is_safe' => ['html']]),
        ];
    }

    public function shareUrl(Shareable $shareable, string $network): string
    {
        return $this->sharer->createUrlForShareable($shareable, $network);
    }

    public function shareLink(Shareable $shareable, string $network, string $caption, array $class = []): string
    {
        $shareNetwork = $this->sharer->getNetwork($network);

        if ($shareNetwork->openShareWindow()) {
            $class[] = 'open-share-window';
        }

        $link = '<a href="%" class="%s">%s</a>';

        $class = array_merge($class, ['share']);

        return sprintf($link, $this->shareUrl($shareable, $network), implode(' ', $class), $caption);
    }

    public function shareDropdownLink(Shareable $shareable, string $network, array $class = []): string
    {
        $shareNetwork = $this->sharer->getNetwork($network);

        if ($shareNetwork->openShareWindow()) {
            $class = array_merge($class, ['open-share-window']);
        }

        $class = array_merge($class, ['share']);

        $style = [
            'background-color: '.$shareNetwork->getBackgroundColor().';',
            'color: '.$shareNetwork->getTextColor().';',
        ];

        $link = '<a href="%s" class="%s" style="%s"><i class="fa %s" aria-hidden="true"></i> %s</a>';

        return sprintf($link, $this->shareUrl($shareable, $network), implode(' ', $class), implode(' ', $style), $shareNetwork->getIcon(), $shareNetwork->getName());
    }

    public function getName(): string
    {
        return 'share_extension';
    }
}
