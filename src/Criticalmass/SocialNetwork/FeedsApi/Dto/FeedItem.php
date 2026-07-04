<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi\Dto;

class FeedItem
{
    public function __construct(
        private readonly int $id,
        private readonly string $uniqueIdentifier,
        private readonly ?string $permalink,
        private readonly ?string $title,
        private readonly string $text,
        private readonly \DateTimeInterface $dateTime,
        private readonly bool $hidden,
        private readonly bool $deleted,
        private readonly \DateTimeInterface $createdAt,
        private readonly ?int $profileId,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    public function getPermalink(): ?string
    {
        return $this->permalink;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getDateTime(): \DateTimeInterface
    {
        return $this->dateTime;
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getProfileId(): ?int
    {
        return $this->profileId;
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            id: $data['id'],
            uniqueIdentifier: $data['uniqueIdentifier'],
            permalink: $data['permalink'] ?? null,
            title: $data['title'] ?? null,
            text: $data['text'],
            dateTime: new \DateTimeImmutable($data['dateTime']),
            hidden: $data['hidden'] ?? false,
            deleted: $data['deleted'] ?? false,
            createdAt: new \DateTimeImmutable($data['createdAt']),
            profileId: $data['profile']['id'] ?? ($data['profile'] ?? null),
        );
    }
}
