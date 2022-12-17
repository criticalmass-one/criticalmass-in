<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Annotation as Routing;
use App\EntityInterface\AuditableInterface;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @Routing\DefaultRoute(name="caldera_criticalmass_location_show")
 * @JMS\ExclusionPolicy("all")
 */
#[ORM\Table(name: 'location')]
#[ORM\Entity(repositoryClass: 'App\Repository\LocationRepository')]
class Location implements RouteableInterface, AuditableInterface, AutoParamConverterAble
{
    /**
     * @JMS\Expose
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    /**
     * @Routing\RouteParameter(name="citySlug")
     */
    #[ORM\ManyToOne(targetEntity: 'City', inversedBy: 'locations')]
    #[ORM\JoinColumn(name: 'city_id', referencedColumnName: 'id')]
    protected ?City $city = null;

    /**
     * @Routing\RouteParameter(name="locationSlug")
     * @JMS\Expose
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $slug = null;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $latitude = null;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(type: 'float', nullable: true)]
    protected ?float $longitude = null;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $title = null;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(type: 'text', nullable: true)]
    protected ?string $description = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLatitude(float $latitude = null): Location
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude = null): Location
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setDescription(string $description = null): Location
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setCity(City $city = null): Location
    {
        $this->city = $city;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setTitle($title = null): Location
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setSlug(string $slug = null): Location
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function hasCoordinates(): bool
    {
        return ($this->latitude && $this->longitude);
    }
}
