<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Sharing\ShareableInterface\Shareable;
use App\Criticalmass\Sharing\SocialSharerInterface;

class SharingExtension extends \Twig_Extension
{
    /** @var SocialSharerInterface $sharer */
    protected $sharer;

    public function __construct(SocialSharerInterface $sharer)
    {
        $this->sharer = $sharer;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('shareUrl', [$this, 'shareUrl']),
            new \Twig_Function('shareLink', [$this, 'shareLink'], ['is_safe' => ['html']]),
            new \Twig_Function('shareDropdownLink', [$this, 'shareDropdownLink'], ['is_safe' => ['html']]),
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
