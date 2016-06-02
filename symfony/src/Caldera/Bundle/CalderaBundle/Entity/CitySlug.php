<?php

namespace Caldera\Bundle\CalderaBundle\Entity;

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
	 */
  protected $id;

	/**
	 * Zeichenkette des eigentlichen Slugs.
	 *
	 * @ORM\Column(type="string", length=50)
     * @JMS\Expose
	 */
	protected $slug;

	/**
	 * Verknuepfte Stadt.
	 *
	 * @ORM\ManyToOne(targetEntity="City", inversedBy="slugs")
	 * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
	 */
	protected $city;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return CitySlug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set city
     *
     * @param City $city
     * @return CitySlug
     */
    public function setCity(City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function __toString()
    {
        return $this->getSlug();
    }
}
