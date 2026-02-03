<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'city_activity')]
#[ORM\Entity(repositoryClass: 'App\Repository\CityActivityRepository')]
#[ORM\Index(fields: ['city', 'createdAt'], name: 'city_activity_city_created_at_idx')]
class CityActivity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id', nullable: false)]
    private City $city;

    #[ORM\Column(type: 'float')]
    private float $score;

    #[ORM\Column(type: 'float')]
    private float $participationScore;

    #[ORM\Column(type: 'integer')]
    private int $participationRawCount;

    #[ORM\Column(type: 'float')]
    private float $photoScore;

    #[ORM\Column(type: 'integer')]
    private int $photoRawCount;

    #[ORM\Column(type: 'float')]
    private float $trackScore;

    #[ORM\Column(type: 'integer')]
    private int $trackRawCount;

    #[ORM\Column(type: 'float')]
    private float $socialFeedScore;

    #[ORM\Column(type: 'integer')]
    private int $socialFeedRawCount;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getScore(): float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getParticipationScore(): float
    {
        return $this->participationScore;
    }

    public function setParticipationScore(float $participationScore): self
    {
        $this->participationScore = $participationScore;

        return $this;
    }

    public function getParticipationRawCount(): int
    {
        return $this->participationRawCount;
    }

    public function setParticipationRawCount(int $participationRawCount): self
    {
        $this->participationRawCount = $participationRawCount;

        return $this;
    }

    public function getPhotoScore(): float
    {
        return $this->photoScore;
    }

    public function setPhotoScore(float $photoScore): self
    {
        $this->photoScore = $photoScore;

        return $this;
    }

    public function getPhotoRawCount(): int
    {
        return $this->photoRawCount;
    }

    public function setPhotoRawCount(int $photoRawCount): self
    {
        $this->photoRawCount = $photoRawCount;

        return $this;
    }

    public function getTrackScore(): float
    {
        return $this->trackScore;
    }

    public function setTrackScore(float $trackScore): self
    {
        $this->trackScore = $trackScore;

        return $this;
    }

    public function getTrackRawCount(): int
    {
        return $this->trackRawCount;
    }

    public function setTrackRawCount(int $trackRawCount): self
    {
        $this->trackRawCount = $trackRawCount;

        return $this;
    }

    public function getSocialFeedScore(): float
    {
        return $this->socialFeedScore;
    }

    public function setSocialFeedScore(float $socialFeedScore): self
    {
        $this->socialFeedScore = $socialFeedScore;

        return $this;
    }

    public function getSocialFeedRawCount(): int
    {
        return $this->socialFeedRawCount;
    }

    public function setSocialFeedRawCount(int $socialFeedRawCount): self
    {
        $this->socialFeedRawCount = $socialFeedRawCount;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
