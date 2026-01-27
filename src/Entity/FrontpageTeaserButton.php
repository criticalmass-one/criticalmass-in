<?php declare(strict_types=1);

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Table(name: 'frontpage_teaser_button')]
#[ORM\Entity]
class FrontpageTeaserButton
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'FrontpageTeaser', inversedBy: 'buttons')]
    #[ORM\JoinColumn(name: 'teaser_id', referencedColumnName: 'id')]
    protected ?FrontpageTeaser $frontpageTeaser = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $caption = null;

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    protected ?string $icon = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $link = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $class = null;

    #[ORM\Column(type: 'smallint', nullable: true)]
    protected int $position = 0;

    #[ORM\Column(type: 'datetime', nullable: false)]
    protected Carbon $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?Carbon $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFrontpageTeaser(FrontpageTeaser $frontpageTeaser): FrontpageTeaserButton
    {
        $this->frontpageTeaser = $frontpageTeaser;

        return $this;
    }

    public function getFrontpageTeaser(): ?FrontpageTeaser
    {
        return $this->frontpageTeaser;
    }

    public function setCaption(?string $caption = null): FrontpageTeaserButton
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setIcon(?string $icon = null): FrontpageTeaserButton
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setLink(?string $link = null): FrontpageTeaserButton
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setClass(?string $class = null): FrontpageTeaserButton
    {
        $this->class = $class;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setPosition(int $position): FrontpageTeaserButton
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setCreatedAt(Carbon $createdAt): FrontpageTeaserButton
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(?Carbon $updatedAt = null): FrontpageTeaserButton
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function __toString(): string
    {
        return sprintf('%s: %s (%d)', $this->caption, $this->link, $this->id);
    }
}
