<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'alert')]
#[ORM\Entity(repositoryClass: 'App\Repository\AlertRepository')]
class Alert
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    private ?string $message = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $fromDateTime = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $untilDateTime = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getFromDateTime(): ?\DateTimeInterface
    {
        return $this->fromDateTime;
    }

    public function setFromDateTime(?\DateTimeInterface $fromDateTime): self
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function getUntilDateTime(): ?\DateTimeInterface
    {
        return $this->untilDateTime;
    }

    public function setUntilDateTime(?\DateTimeInterface $untilDateTime): self
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }
}
