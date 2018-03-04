<?php

namespace Criticalmass\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="feed_item", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="unique_feed_item", columns={"social_network_profile_id", "uniqueIdentifier"})
 *    })
 * @ORM\Entity(repositoryClass="Criticalmass\Bundle\AppBundle\Repository\FeedItemRepository")
 */
class FeedItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SocialNetworkProfile", inversedBy="feedItems")
     * @ORM\JoinColumn(name="social_network_profile_id", referencedColumnName="id")
     */
    protected $socialNetworkProfile;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $uniqueIdentifier;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $text;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $dateTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): FeedItem
    {
        $this->id = $id;

        return $this;
    }

    public function getSocialNetworkProfile(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }

    public function setSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): FeedItem
    {
        $this->socialNetworkProfile = $socialNetworkProfile;

        return $this;
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier(string $uniqueIdentifier): FeedItem
    {
        $this->uniqueIdentifier = $uniqueIdentifier;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): FeedItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): FeedItem
    {
        $this->text = $text;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): FeedItem
    {
        $this->dateTime = $dateTime;

        return $this;
    }


}
