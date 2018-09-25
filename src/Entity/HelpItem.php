<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="helpItems")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

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
    protected $position = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): HelpItem
    {
        $this->user = $user;

        return $this;
    }


    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): HelpItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
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

    public function getCategory(): ?HelpCategory
    {
        return $this->category;
    }

    public function setCategory(HelpCategory $category): HelpItem
    {
        $this->category = $category;

        return $this;
    }
}
