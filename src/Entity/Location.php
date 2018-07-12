<?php declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\AuditableInterface;
use App\EntityInterface\AutoParamConverterAble;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Criticalmass\Router\Annotation as Routing;

/**
 * @ORM\Table(name="location")
 * @ORM\Entity(repositoryClass="App\Repository\LocationRepository")
 * @Routing\DefaultRoute(name="caldera_criticalmass_location_show")
 */
class Location implements RouteableInterface, AuditableInterface, AutoParamConverterAble
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="photos")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @Routing\RouteParameter(name="citySlug")
     */
    protected $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Routing\RouteParameter(name="locationSlug")
     */
    protected $slug;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    protected $longitude;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

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
