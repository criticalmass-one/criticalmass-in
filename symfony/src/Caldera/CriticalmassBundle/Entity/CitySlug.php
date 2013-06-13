<?php

namespace Caldera\CriticalmassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="cityslug")
 */
class CitySlug
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
  protected $id;

	/**
	 * @ORM\Column(type="string", length=50)
	 */
	protected $slug;

	/**
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
     * @param \Caldera\CriticalmassBundle\Entity\City $city
     * @return CitySlug
     */
    public function setCity(\Caldera\CriticalmassBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \Caldera\CriticalmassBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }
}
