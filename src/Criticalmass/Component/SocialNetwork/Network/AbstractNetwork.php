<?php

namespace Criticalmass\Component\SocialNetwork\Network;

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
}
