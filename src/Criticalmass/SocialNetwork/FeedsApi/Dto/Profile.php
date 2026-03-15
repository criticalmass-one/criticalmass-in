<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedsApi\Dto;

class Profile
{
    public function __construct(
        private readonly int $id,
        private readonly string $identifier,
        private readonly ?string $networkIdentifier,
        private readonly ?int $networkId,
        private readonly ?\DateTimeInterface $createdAt,
        private readonly ?\DateTimeInterface $lastFetchSuccessDateTime,
        private readonly ?\DateTimeInterface $lastFetchFailureDateTime,
        private readonly ?string $lastFetchFailureError,
        private readonly bool $autoFetch,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getNetworkIdentifier(): ?string
    {
        return $this->networkIdentifier;
    }

    public function getNetworkId(): ?int
    {
        return $this->networkId;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getLastFetchSuccessDateTime(): ?\DateTimeInterface
    {
        return $this->lastFetchSuccessDateTime;
    }

    public function getLastFetchFailureDateTime(): ?\DateTimeInterface
    {
        return $this->lastFetchFailureDateTime;
    }

    public function getLastFetchFailureError(): ?string
    {
        return $this->lastFetchFailureError;
    }

    public function isAutoFetch(): bool
    {
        return $this->autoFetch;
    }

    public static function fromApiResponse(array $data): self
    {
        return new self(
            id: $data['id'],
            identifier: $data['identifier'],
            networkIdentifier: $data['network']['identifier'] ?? null,
            networkId: $data['network']['id'] ?? null,
            createdAt: isset($data['createdAt']) ? new \DateTimeImmutable($data['createdAt']) : null,
            lastFetchSuccessDateTime: isset($data['lastFetchSuccessDateTime']) ? new \DateTimeImmutable($data['lastFetchSuccessDateTime']) : null,
            lastFetchFailureDateTime: isset($data['lastFetchFailureDateTime']) ? new \DateTimeImmutable($data['lastFetchFailureDateTime']) : null,
            lastFetchFailureError: $data['lastFetchFailureError'] ?? null,
            autoFetch: $data['autoFetch'] ?? true,
        );
    }
}
