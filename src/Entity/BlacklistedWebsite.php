<?php declare(strict_types=1);

namespace App\Entity;

use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blacklisted_website')]
#[ORM\Entity]
class BlacklistedWebsite
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    protected ?string $pattern = null;

    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    protected ?Carbon $createdAt = null;

    public function __construct()
    {
        $this->createdAt = Carbon::now();
    }

    public function getId():? int
    {
        return $this->id;
    }

    public function setId(int $id): BlacklistedWebsite
    {
        $this->id = $id;

        return $this;
    }

    public function getPattern(): ?string
    {
        return $this->pattern;
    }

    public function setPattern(string $pattern): BlacklistedWebsite
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): BlacklistedWebsite
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function setCreatedAt(Carbon $createdAt): BlacklistedWebsite
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
