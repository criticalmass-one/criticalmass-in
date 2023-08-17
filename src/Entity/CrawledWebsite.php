<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'crawled_website')]
#[ORM\Entity]
class CrawledWebsite
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $url = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $imageUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $title = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    protected \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId():? int
    {
        return $this->id;
    }

    public function setId(int $id): CrawledWebsite
    {
        $this->id = $id;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): CrawledWebsite
    {
        $this->url = $url;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): CrawledWebsite
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title = null): CrawledWebsite
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description = null): CrawledWebsite
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): CrawledWebsite
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
