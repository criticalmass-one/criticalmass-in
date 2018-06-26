<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CitySlugRepository")
 * @ORM\Table(name="cityslug")
 * @JMS\ExclusionPolicy("all")
 */
class CitySlug
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="slugs")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    public function __construct()
    {

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
