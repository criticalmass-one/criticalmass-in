<?php declare(strict_types=1);

namespace App\Entity;

use MalteHuebner\DataQueryBundle\Annotation\EntityAnnotation as DataQuery;
use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="App\Repository\PromotionRepository")
 * @Routing\DefaultRoute(name="caldera_criticalmass_promotion_show")
 */
class Promotion implements AutoParamConverterAble, ViewableEntity, RouteableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Routing\RouteParameter(name="promotionSlug")
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $query = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $showMap = false;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $mapCenterLatitude = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $mapCenterLongitude = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $mapZoomLevel = null;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     * @DataQuery\Sortable
     * @DataQuery\Queryable
     */
    protected int $views = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function hasMap(): ?bool
    {
        return $this->showMap;
    }

    public function getShowMap(): ?bool
    {
        return $this->showMap;
    }

    public function setShowMap(bool $showMap): self
    {
        $this->showMap = $showMap;

        return $this;
    }

    public function getMapCenterLatitude(): ?float
    {
        return $this->mapCenterLatitude;
    }

    public function setMapCenterLatitude(?float $mapCenterLatitude): self
    {
        $this->mapCenterLatitude = $mapCenterLatitude;

        return $this;
    }

    public function getMapCenterLongitude(): ?float
    {
        return $this->mapCenterLongitude;
    }

    public function setMapCenterLongitude(?float $mapCenterLongitude): self
    {
        $this->mapCenterLongitude = $mapCenterLongitude;

        return $this;
    }

    public function getMapZoomLevel(): ?int
    {
        return $this->mapZoomLevel;
    }

    public function setMapZoomLevel(?int $mapZoomLevel): self
    {
        $this->mapZoomLevel = $mapZoomLevel;

        return $this;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    public function incViews(): ViewableEntity
    {
        ++$this->views;

        return $this;
    }

    public function setViews(int $views): ViewableEntity
    {
        $this->views = $views;

        return $this;
    }
}
