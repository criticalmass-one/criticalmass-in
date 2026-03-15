<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi\Dto;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;

class Network implements NetworkInterface
{
    public function __construct(
        private readonly int $id,
        private readonly string $identifier,
        private readonly string $name,
        private readonly string $icon,
        private readonly string $backgroundColor,
        private readonly string $textColor,
        private readonly string $profileUrlPattern,
    ) {
    }

    public function getFeedsApiId(): int
    {
        return $this->id;
    }

    public function getIri(): string
    {
        return sprintf('/api/networks/%d', $this->id);
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
        return $this->identifier;
    }

    public function getDetectorPriority(): int
    {
        if ($this->identifier === 'homepage') {
            return -100;
        }

        if ($this->identifier === 'youtube_video') {
            return -10;
        }

        return 0;
    }

    public function accepts(string $url): bool
    {
        return (bool) preg_match($this->profileUrlPattern, $url);
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            id: $data['id'],
            identifier: $data['identifier'],
            name: $data['name'],
            icon: $data['icon'],
            backgroundColor: $data['backgroundColor'],
            textColor: $data['textColor'],
            profileUrlPattern: $data['profileUrlPattern'] ?? '#^$#',
        );
    }
}
