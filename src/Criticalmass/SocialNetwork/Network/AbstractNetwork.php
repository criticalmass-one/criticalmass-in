<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

abstract class AbstractNetwork implements NetworkInterface
{
    protected string $name;

    protected string $icon;

    protected string $backgroundColor;

    protected string $textColor;

    protected int $detectorPriority = 0;

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

        return $this->camelCaseToUnderscore($reflection->getShortName());
    }

    protected function camelCaseToUnderscore(string $input, bool $avoidDoubleUnderscore = true): string
    {
        $output = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));

        if ($avoidDoubleUnderscore) {
            $output = str_replace('__', '_', $output);
        }

        return $output;
    }

    public function getDetectorPriority(): int
    {
        return $this->detectorPriority;
    }

    public function accepts(string $url): bool
    {
        return false;
    }
}
