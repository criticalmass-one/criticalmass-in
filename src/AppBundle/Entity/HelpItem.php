<?php

namespace AppBundle\Entity;

use AppBundle\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="help_item")
 * @ORM\Entity()
 */
class HelpItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="HelpCategory", inversedBy="items")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    protected $text;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $position;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): HelpItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): HelpItem
    {
        $this->text = $text;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): HelpItem
    {
        $this->position = $position;

        return $this;
    }
}
