<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Diese Entitaet stellt einen so genannten Slug einer Stadt dar, ueber den die
 * Stadt unter anderem innerhalb der Adresse von criticalmass.in zugreifbar
 * ist. So laesst sich beispielsweise die Hamburger Critical Mass ueber
 * criticalmass.in/hamburg aufrufen, waehrend die Koelner Critical Mass aus
 * historischen Gruenden gleich fuenf verschiedene Aufrufe kennt.
 *
 * Einer Stadt koennen beliebig viele Slugs zugeordnet werden, wenigstens einer
 * ist jedoch fuer die Funktionstuechtigkeit einer Stadt notwendig.
 *
 * @ORM\Entity()
 * @ORM\Table(name="cityslug")
 * @JMS\ExclusionPolicy("all")
 */
class CitySlug
{
    /**
     * ID der Entitaet.
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $id;

    /**
     * Zeichenkette des eigentlichen Slugs.
     *
     * @ORM\Column(type="string", length=50)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $slug;

    /**
     * Verknuepfte Stadt.
     *
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
