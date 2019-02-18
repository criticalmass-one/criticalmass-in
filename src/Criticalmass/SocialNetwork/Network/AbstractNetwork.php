<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

use App\Entity\SocialNetworkProfile;
use App\Criticalmass\Util\StringUtil;

abstract class AbstractNetwork implements NetworkInterface
{
    /** @var string $name */
    protected $name;

    /** @var string $icon */
    protected $icon;

    /** @var string $backgroundColor */
    protected $backgroundColor;

    /** @var string $textColor */
    protected $textColor;

    /** @var int $detectorPriority */
    protected $detectorPriority = 0;

    public function __construct()
    {
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

    public function getIdentifier(): string
    {
        $reflection = new \ReflectionClass($this);

        return StringUtil::camelCaseToUnderscore($reflection->getShortName());
    }

    public function getDetectorPriority(): int
    {
        return $this->detectorPriority;
    }

    public function accepts(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return false;
    }
}
