<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Annotation as Routing;
use App\EntityInterface\RouteableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CitySlugRepository")
 * @ORM\Table(name="cityslug")
 * @JMS\ExclusionPolicy("all")
 */
class CitySlug implements RouteableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?int $id = null;

    /**
     * @ORM\Column(type="string", length=50)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     * @Routing\RouteParameter(name="citySlug")
     */
    protected ?string $slug = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="slugs", fetch="EAGER")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected ?City $city = null;

    public function __construct(string $slug = null)
    {
        $this->slug = $slug;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): CitySlug
    {
        $this->city = $city;

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug = null): CitySlug
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getSlug();
    }
}
