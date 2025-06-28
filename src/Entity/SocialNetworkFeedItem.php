<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Table(name: 'social_network_feed_item')]
#[ORM\UniqueConstraint(name: 'unique_feed_item', columns: ['social_network_profile_id', 'uniqueIdentifier'])]
#[ORM\Entity(repositoryClass: 'App\Repository\SocialNetworkFeedItemRepository')]
#[ORM\Index(fields: ['dateTime'], name: 'social_network_feed_item_date_time_index')]
#[ORM\Index(fields: ['createdAt'], name: 'social_network_feed_item_created_at_index')]
class SocialNetworkFeedItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'SocialNetworkProfile', inversedBy: 'feedItems')]
    #[ORM\JoinColumn(name: 'social_network_profile_id', referencedColumnName: 'id')]
    #[SerializedName('social_network_profile_id')]
    #[Ignore]
    protected ?SocialNetworkProfile $socialNetworkProfile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    protected ?string $uniqueIdentifier = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $permalink = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: 'text', nullable: false)]
    protected ?string $text = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected ?\DateTime $dateTime = null;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $hidden = false;

    #[ORM\Column(type: 'boolean', nullable: false)]
    protected bool $deleted = false;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected \DateTime $createdAt;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $raw = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): SocialNetworkFeedItem
    {
        $this->id = $id;

        return $this;
    }

    public function getSocialNetworkProfile(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }

    public function setSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): SocialNetworkFeedItem
    {
        $this->socialNetworkProfile = $socialNetworkProfile;

        return $this;
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier(string $uniqueIdentifier): SocialNetworkFeedItem
    {
        $this->uniqueIdentifier = $uniqueIdentifier;

        return $this;
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): SocialNetworkFeedItem
    {
        $this->permalink = $permalink;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): SocialNetworkFeedItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): SocialNetworkFeedItem
    {
        $this->text = $text;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): SocialNetworkFeedItem
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): SocialNetworkFeedItem
    {
        $this->hidden = $hidden;

        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): SocialNetworkFeedItem
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): SocialNetworkFeedItem
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRaw(): ?string
    {
        return $this->raw;
    }

    public function setRaw(string $raw): SocialNetworkFeedItem
    {
        $this->raw = $raw;
        
        return $this;
    }

}
