<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\PhotoInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Table(name="frontpage_teaser")
 * @ORM\Entity(repositoryClass="App\Repository\FrontpageTeaserRepository")
 * @Vich\Uploadable
 */
class FrontpageTeaser implements PhotoInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="frontpageTeasers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="frontpageTeasers")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected ?City $city = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $headline = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected ?string $text = null;

    /**
     * @Vich\UploadableField(mapping="frontpage_teaser", fileNameProperty="imageName",  size="imageSize", mimeType="imageMimeType")
     */
    protected ?File $imageFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageName = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $imageSize = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageMimeType = null;

    /**
     * @ORM\Column(type="smallint")
     */
    protected int $position = 0;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected \DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $validFrom = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTime $validUntil = null;

    /**
     * @ORM\OneToMany(targetEntity="FrontpageTeaserButton", mappedBy="frontpageTeaser")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected Collection $buttons;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->buttons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user): FrontpageTeaser
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setCity(City $city = null): FrontpageTeaser
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setHeadline(string $headline = null): FrontpageTeaser
    {
        $this->headline = $headline;

        return $this;
    }

    public function getHeadline(): ?string
    {
        return $this->headline;
    }

    public function setText(string $text): FrontpageTeaser
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setImageFile(File $image = null): PhotoInterface
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(string $imageName = null): PhotoInterface
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(int $imageSize = null): PhotoInterface
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageMimeType(): ?string
    {
        return $this->imageMimeType;
    }

    public function setImageMimeType(string $imageMimeType = null): PhotoInterface
    {
        $this->imageMimeType = $imageMimeType;

        return $this;
    }

    public function setPosition(int $position): FrontpageTeaser
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setCreatedAt(\DateTime $createdAt): FrontpageTeaser
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): FrontpageTeaser
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setValidFrom(\DateTime $validFrom = null): FrontpageTeaser
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getValidFrom(): ?\DateTime
    {
        return $this->validFrom;
    }

    public function setValidUntil(\DateTime $validUntil = null): FrontpageTeaser
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    public function getValidUntil(): ?\DateTime
    {
        return $this->validUntil;
    }

    public function __toString(): string
    {
        return sprintf('%s (%d)', $this->headline, $this->id);
    }

    public function addButton(FrontpageTeaserButton $button): FrontpageTeaser
    {
        $this->buttons->add($button);

        return $this;
    }

    public function setButtons(Collection $buttons): FrontpageTeaser
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getButtons(): Collection
    {
        return $this->buttons;
    }

    public function removeButton(FrontpageTeaserButton $button): FrontpageTeaser
    {
        $this->buttons->removeElement($button);

        return $this;
    }
}
