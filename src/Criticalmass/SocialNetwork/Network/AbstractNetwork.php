<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Network;

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
