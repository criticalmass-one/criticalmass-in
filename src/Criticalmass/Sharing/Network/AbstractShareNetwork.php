<?php declare(strict_types=1);

namespace App\Criticalmass\Sharing\Network;

use App\Criticalmass\Sharing\Metadata\Metadata;
use App\Criticalmass\Sharing\ShareableInterface\Shareable;

abstract class AbstractShareNetwork implements ShareNetworkInterface
{
    /** @var string $name */
    protected $name;

    /** @var string $icon */
    protected $icon;

    /** @var string $backgroundColor */
    protected $backgroundColor;

    /** @var string $textColor */
    protected $textColor;

    /** @var Metadata $metadata */
    protected $metadata;

    /** @var bool $openSharewindow */
    protected $openSharewindow;

    public function __construct(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public function getIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);

        $shortname = $reflection->getShortName();

        $identifier = strtolower(str_replace('ShareNetwork', '', $shortname));

        return $identifier;
    }

    protected function getShareUrl(Shareable $shareable): string
    {
        $shareableUrl = $this->metadata->getShareUrl($shareable);

        return str_replace('http://', 'https://', $shareableUrl);
    }

    protected function getShareTitle(Shareable $shareable): ?string
    {
        return $this->metadata->getShareTitle($shareable);
    }

    protected function getShareIntro(Shareable $shareable): ?string
    {
        return $this->metadata->getShareIntro($shareable);
    }
    
    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getBackgroundColor(): string
    {
        return $this->backgroundColor;
    }

    public function getTextColor(): string
    {
        return $this->textColor;
    }

    public function openShareWindow(): bool
    {
        return $this->openSharewindow;
    }
}
